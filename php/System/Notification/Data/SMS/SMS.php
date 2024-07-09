<?php
namespace System\Notification\Data\SMS;

class SMS
{
   private int $sender_id;
   private int $recipient_id;

   private SMSBody $smsBody;

   public function __construct(int $sender_id, int $recipient_id, SMSBody $smsBody)
   {
        $this->sender_id = $sender_id;
        $this->recipient_id = $recipient_id;
        $this->smsBody = $smsBody;
   }

   public static function getInstance(int $sender_id, int $recipient_id, SMSBody $smsBody): SMS
   {
        return new SMS($sender_id, $recipient_id, $smsBody);
   }


   public function getSenderId(): int
   {
        return $this->sender_id;
   }

   public function setSenderId(int $sender_id): static
   {
        $this->sender_id = $sender_id;

        return $this;
   }

   public function getRecipientId(): int
   {
        return $this->recipient_id;
   }

   public function setRecipientId(int $recipient_id): static
   {
        $this->recipient_id = $recipient_id;

        return $this;
   }

   public function getSMSBody(): SMSBody
   {
        return $this->smsBody;
   }

   public function setSMSBody(SMSBody $smsBody): static
   {
        $this->smsBody = $smsBody;

        return $this;
   }

}