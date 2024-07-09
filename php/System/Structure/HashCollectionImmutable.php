<?php
namespace System\Structure;

class HashCollectionImmutable
{
    private array $data = [];

    public function __construct(array $params)
    {
        foreach ($params as $param_key => $param_value) {
            $this->data[$param_key] = $param_value;
        }
    }

    public static function getInstance(array $params): HashCollectionImmutable
    {
        return new HashCollectionImmutable($params);
    }


    public function __get($name): ?mixed
    {
        return (array_key_exists($name, $this->data)) ? $this->data[$name] : null;
    }

    public function getArray()
    {
        return $this->data;
    }
    

}