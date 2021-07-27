<?php

namespace App\Support;

class Message
{
    private $type;
    private $message;

    /** Getters dos atributos */
    public function getMessage()
    {
        return $this->message;
    }
    public function getType()
    {
        return $this->type;
    }

    /** Recebe, seta a mensagem e o tipo  */
    public function error(string $message): Message
    {
        $this->type = 'error';
        $this->message = $message;

        return $this;
    }

    /** Recebe, seta a mensagem e o tipo  */
    public function success(string $message): Message
    {
        $this->type = 'success';
        $this->message = $message;

        return $this;
    }

    public function info(string $message): Message
    {
        $this->type = 'info';
        $this->message = $message;

        return $this;
    }

    public function warning(string $message): Message
    {
        $this->type = 'warning';
        $this->message = $message;

        return $this;
    }

    /** Usa os próprios atributos da classe */
    public function render()
    {
        return "<div class='message {$this->getType()}'>{$this->getMessage()}</div>";
    }

}