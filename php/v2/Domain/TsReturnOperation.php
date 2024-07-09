<?php

namespace NW\WebService\References\Operations\Notification\Domain;
use System\Factory\HttpFactory\HandlerHttpFactory;
use NW\WebService\References\Operations\Notification\DTO\RequestDTO\TsReturnOperationRequestDTO;
use System\Notification\Data\Email\Email;
use System\Notification\Data\Email\Subject;
use System\Notification\Data\Email\Message;
use System\Notification\Data\SMS\SMS;
use System\Notification\Data\SMS\SMSBody;
use System\Notification\Client\MessagesClient;
use System\Notification\Client\NotificationManager;

use NW\WebService\References\Operations\Notification\Entity\Contractor;
use NW\WebService\References\Operations\Notification\Entity\Client;
use NW\WebService\References\Operations\Notification\Entity\Employee;
use NW\WebService\References\Operations\Notification\Entity\Seller;

use NW\WebService\References\Operations\Notification\Dictionary\NotificationEvents;
use NW\WebService\References\Operations\Notification\Dictionary\Status;

use NW\WebService\References\Operations\Notification\Helper\NotificationHelper;


class TsReturnOperation extends ReferencesOperation
{
    public const TYPE_NEW    = 1;
    public const TYPE_CHANGE = 2;
    
    private TsReturnOperationRequestDTO $data;

    public function __construct()
    {
        $request = HandlerHttpFactory::makeRequestHandler()->makeFromPost();
        $this->data = TsReturnOperationRequestDTO::getInstance($request);
    }

    /**
     * @throws \Exception
     */
    public function doOperation(): array
    {

        $data = (array)$this->getRequest('data');
        $reseller_id = $this->data->getResselerId();

        $notificationType = $this->data->getNotificationType();
        $result = [
            'notificationEmployeeByEmail' => false,
            'notificationClientByEmail'   => false,
            'notificationClientBySms'     => [
                'isSent'  => false,
                'message' => '',
            ],
        ];

        if (!$reseller_id) {
            $result['notificationClientBySms']['message'] = 'Empty resellerId';
            return $result;
        }

        $reseller = Seller::getById($reseller_id);
        if ($reseller === null) {
            throw new \Exception('Seller not found!', 400);
        }

        $client = Client::getById($this->data->getClientId());
        if ($client === null || $client->type !== Contractor::TYPE_CUSTOMER || $client->seller->id !== $reseller_id) {
            throw new \Exception('сlient not found!', 400);
        }

        $cFullName = $client->getFullName();
        if (empty($client->getFullName())) {
            $cFullName = $client->name;
        }

        $cr = Employee::getById($this->data->getCreatorId());
        if ($cr === null) {
            throw new \Exception('Creator not found!', 400);
        }

        $et = Employee::getById($this->data->getExpertId());
        if ($et === null) {
            throw new \Exception('Expert not found!', 400);
        }

        $differences = $this->getDifferencesByStatus($notificationType, $this->data);

        $templateData = [
            'COMPLAINT_ID'       => $this->data->getComplaintId(),
            'RESELLER_ID'       => $this->data->getResselerId(),
            'COMPLAINT_NUMBER'   => $this->data->getComplaintNumber(),
            'CREATOR_ID'         => $this->data->getCreatorId(),
            'CREATOR_NAME'       => $cr->getFullName(),
            'EXPERT_ID'          => $this->data->getExpertId(),
            'EXPERT_NAME'        => $et->getFullName(),
            'CLIENT_ID'          => $this->data->getClientId(),
            'CLIENT_NAME'        => $cFullName,
            'CONSUMPTION_ID'     => $this->data->getConsumptionId(),
            'CONSUMPTION_NUMBER' => $this->data->getConsumptionNumber(),
            'AGREEMENT_NUMBER'   => $this->data->getAgreementNumber(),
            'DATE'               => $this->data->getDate(),
            'DIFFERENCES'        => $differences,
        ];

        // Если хоть одна переменная для шаблона не задана, то не отправляем уведомления
        foreach ($templateData as $key => $tempData) {
            if (empty($tempData)) {
                throw new \Exception("Template Data ({$key}) is empty!", 500);
            }
        }
        
        $emailFrom = NotificationHelper::getResellerEmailFrom($reseller_id);
        // Получаем email сотрудников из настроек
        $emails = NotificationHelper::getEmailsByPermit($reseller_id, 'tsGoodsReturn');
        if (!empty($emailFrom) && count($emails) > 0) {
            $subject = Subject::getInstance('complaintEmployeeEmailSubject', "reseller_id: {$reseller_id}");
            $message = Message::getInstance('complaintEmployeeEmailBody', $templateData, "reseller_id: {$reseller_id}");

            foreach ($emails as $email) {
                $email = Email::getInstance($subject, $message, $emailFrom, $email);

                MessagesClient::sendMessage($email, NotificationEvents::CHANGE_RETURN_STATUS);
                $result['notificationEmployeeByEmail'] = true;

            }
        }

        // Шлём клиентское уведомление, только если произошла смена статуса
        if ($notificationType === self::TYPE_CHANGE && !empty($data['differences']['to'])) {
            if ($emailFrom && $client->email) {
                
                $subject = Subject::getInstance('complaintClientEmailSubject', "reseller_id: {$reseller_id}");
                $message = Message::getInstance('complaintClientEmailBody', $templateData, "reseller_id: {$reseller_id}");
                $email = Email::getInstance($subject, $message, $emailFrom, $client->email);

                MessagesClient::sendMessage($email, NotificationEvents::CHANGE_RETURN_STATUS);

                $result['notificationClientByEmail'] = true;
            }

            if ($client->mobile) {

                $smsBody = SMSBody::getInstance("notify sms message", $templateData, "reseller_id: {$reseller_id}");  
                $sms = SMS::getInstance($reseller_id, $client->id, $smsBody);

                $res = NotificationManager::send($sms, NotificationEvents::CHANGE_RETURN_STATUS);
                if ($res) {
                    $result['notificationClientBySms']['isSent'] = true;
                }
                else {
                    $result['notificationClientBySms']['message'] = $res;
                }
            }
        }

        return $result;
    }


    private function getDifferencesByStatus(int $notificationType, TsReturnOperationRequestDTO $data): array
    {
        $differences = [];

        if ($notificationType === self::TYPE_NEW) {
            $differences = ['NewPositionAdded', null, $data->getResselerId()];
        } 

        else if ($notificationType === self::TYPE_CHANGE) {

            if (!$data->getDifferences()) {
                throw new \Exception("param 'differences' must be specified for change notification in request", 400);
            }


            $from_numeric_status = $this->data->getDifferences()->from;
            $to_numeric_status = $this->data->getDifferences()->to;

            if (!$from_numeric_status) {
                throw new \Exception("from status not specified in request", 400);
            }

            if (!$to_numeric_status) {
                throw new \Exception("to status not specified in request", 400);
            }

            $from_name_status = Status::getName($from_numeric_status);
            $to_name_status = Status::getName($to_numeric_status);

            if (!$from_name_status) {
                throw new \Exception("Unknown 'from' status", 400);
            }

            if (!$to_name_status) {
                throw new \Exception("Unknow 'to' status", 400);
            }

            $differences = ['PositionStatusHasChanged', [
                    'FROM' => $from_name_status,
                    'TO'   => $to_name_status,
                ], $data->getResselerId()];

        }

        return $differences;

    }


}
