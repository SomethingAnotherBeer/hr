<?php
namespace System\Notification\Data\Email;

class Message
{
    private string $message;
    private string $hash_params;
    private string $additional_params;

    public function __construct(string $message, array $hash_params = [], string ...$additional_params)
    {
        $this->message = $message;

        $prepared_hash_params = [];

        foreach ($hash_params as $hash_param_key => $hash_param_value) {
            $prepared_hash_params[] = $hash_param_key . ":" . $hash_param_value;
        }

        $this->hash_params = implode("\n", $prepared_hash_params);


        $this->hash_params = implode("\n", $prepared_hash_params);
        $this->additional_params = implode("\n", $additional_params);

    }


    public static function getInstance(string $message, array $hash_params = [], string ...$additional_params): Message
    {
        return new Message($message, $hash_params, ...$additional_params);
    }


    public function getContent(): string
    {
        return $this->message . "\n" . $this->hash_params . "\n" . $this->additional_params;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }


    public function getHashParams(): string
    {
        return $this->hash_params;
    }

    public function setHashParams(array $hash_params): static
    {
        $prepared_hash_params = [];

        foreach ($hash_params as $hash_param_key => $hash_param_value) {
            $prepared_hash_params[] = $hash_param_key . ": " . $hash_param_value;
        }

        $this->hash_params = implode("\n", $prepared_hash_params);

        return $this;
    }

    public function getAdditionalParams(): string
    {
        return $this->additional_params;
    }

    public function setAdditionalParams(string ...$additional_params): static
    {
        $this->additional_params = implode("\n", $additional_params);

        return $this;
    }

}