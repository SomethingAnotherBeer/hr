<?php
namespace NW\WebService\References\Operations\Notification\Entity;

/**
 * @property Seller $Seller
 */
class Contractor
{
    const TYPE_CUSTOMER = 0;
    public $id;
    public $type;
    public $email;
    public $name;

    public static function getById(int $resellerId): static
    {
        return new static($resellerId); // fakes the getById method
    }

    public function getFullName(): string
    {
        return $this->name . ' ' . $this->id;
    }
}