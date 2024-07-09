<?php
namespace NW\WebService\References\Operations\Notification\Helper;

class NotificationHelper
{
    public static function getResellerEmailFrom(): string
    {
        return 'contractor@example.com';
    }

    public static function getEmailsByPermit($resellerId, $event): array
    {
        // fakes the method
        return ['someemail@example.com', 'someemail2@example.com'];
    }

}