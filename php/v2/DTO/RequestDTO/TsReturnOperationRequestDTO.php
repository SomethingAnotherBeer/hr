<?php
namespace NW\WebService\References\Operations\Notification\DTO\RequestDTO;
use Model\DTO;
use System\Http\Data\Request;
use System\Structure\HashCollectionImmutable;
use Exception\ValidationException;

class TsReturnOperationRequestDTO extends DTO
{
    private ?int $reseller_id = null;
    private ?int $notification_type = null;
    private ?int $client_id = null;
    private ?int $creator_id = null;
    private ?int $expert_id = null;
    private ?int $complaint_id = null;
    private ?string $complaint_number = null;
    private ?int $consumption_id = null;
    private ?string $consumption_number = null;
    private ?string $agreement_number = null;
    private ?string $date = null;

    private ?HashCollectionImmutable $differences = null;

    public function __construct(Request $request)
    {
        $data = $request->data;

        if (!$data) {
            throw new ValidationException("Param 'data' is required in request");            
        }

        $required_params = 
        [
            'notification_type',
            'client_id',
            'creator_id',
            'expert_id',
            'complaint_number',
            'complaint_id',
            'consumption_id',
            'consumption_number',
            'agreement_number',
            'date',
        ];

        $numeric_params = 
        [
            'notification_type',
            'client_id',
            'creator_id',
            'expert_id',
            'complaint_id',
            'consumption_id',
        ];

        $string_params =
        [
            'complaint_number',
            'consumption_number',
            'agreement_number',
            'date',
        ];

        $this->checkNotNullOrEmptyByRequired($data, $required_params);
        $this->checkNumericByRequired($data, $numeric_params);
        $this->checkIsStringByRequired($data, $string_params);

        $this->reseller_id = (int)$data->reseller_id;
        $this->notification_type = (int)$data->notification_type;
        $this->client_id = (int)$data->client_id;
        $this->creator_id = (int)$data->creator_id;
        $this->expert_id = (int)$data->expert_id;
        $this->complaint_id = (int)$data->complaint_id;
        $this->complaint_number = (string)$data->complaint_number;
        $this->consumption_id = (int)$data->consumption_id;
        $this->consumption_number = (string)$data->consumption_number;
        $this->agreement_number = (string)$data->agreement_number;
        $this->date = (string)$data->date;
        $this->differences = $data->differences;
    }

    public function getResselerId(): ?int
    {
        return $this->reseller_id;
    }

    public function getNotificationType(): ?int
    {
        return $this->notification_type;
    }

    public function getClientId(): ?int
    {
        return $this->client_id;
    }

    public function getCreatorId(): ?int
    {
        return $this->creator_id;
    }

    public function getExpertId(): ?int
    {
        return $this->expert_id;
    }


    public function getComplaintId(): ?int
    {
        return $this->complaint_id;
    }

    public function getComplaintNumber(): ?string
    {
        return $this->complaint_number;
    }

    public function getConsumptionId(): ?int
    {
        return $this->consumption_id;
    }

    public function getConsumptionNumber(): ?string
    {
        return $this->consumption_number;
    }

    public function getAgreementNumber(): ?string
    {
        return $this->agreement_number;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function getDifferences(): ?HashCollectionImmutable
    {
        return $this->differences;
    }


}