<?php
namespace System\Notification\Data\Email;

class Email
{

    private string $from;
    private string $to;

    private Subject $subject;
    private Message $message;

    public function __construct(Subject $subject, Message $message, string $from, string $to)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->from = $from;
        $this->to = $to;
    }

    public static function getInstance(Subject $subject, Message $message, string $from, string $to): Email
    {
        return new Email($subject, $message, $from, $to);
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function setFrom(string $from): static
    {
        $this->from = $from;

        return $this;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function setTo(string $to): static
    {
        $this->to = $to;

        return $this;
    }

    public function getSubject(): Subject
    {
        return $this->subject;
    }

    public function setSubject(Subject $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

    public function setMessage(Message $message): static
    {
        $this->message = $message;

        return $this;
    }

}