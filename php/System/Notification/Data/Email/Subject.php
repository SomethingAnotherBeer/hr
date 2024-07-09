<?php
namespace System\Notification\Data\Email;

class Subject
{
    private string $subject;
    private ?string $additional_params = null;

    public function __construct(string $subject, string ...$additional_params)
    {
        $this->subject = $subject;
        $this->additional_params = implode(', ', $additional_params);
    }
    
    public static function getInstance(string $subject, string ...$additional_params): Subject
    {
        return new Subject($subject, ...$additional_params);
    }


    public function getContent(): string
    {
        return $this->subject . ' ' . $this->additional_params;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    
    public function getAdditionalParams(): string
    {
        return $this->additional_params;
    }

    public function setAdditionalParams(string ...$additional_params): static
    {
        $this->additional_params = implode(', ', $additional_params);

        return $this;
    }


}