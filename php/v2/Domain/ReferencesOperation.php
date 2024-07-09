<?php
namespace NW\WebService\References\Operations\Notification\Domain;
use System\Http\Data\Request;


abstract class ReferencesOperation
{
    protected Request $request;

    abstract public function doOperation(): array;

    public function getRequest($pName)
    {
        return $_REQUEST[$pName];
    }
}