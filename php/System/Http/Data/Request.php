<?php
namespace System\Http\Data;

class Request
{
    private array $data = [];

    public function __construct(array $params = [])
    {
        foreach ($params as $param_key => $param_value) {
            $this->data[$param_key] = $param_value;
        }
    }


    public static function getInstance(array $params = []): Request
    {
        return new Request($params);
    }

    public function __get(string $name): ?mixed
    {   
        return (array_key_exists($name, $this->data)) ? $this->data[$name] : null;
    }

    public function __set($name, $value): void
    {
        $this->data[$name] = $value;
    }

}