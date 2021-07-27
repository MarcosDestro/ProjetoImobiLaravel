<?php

namespace App\Mail\Web;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Contact extends Mailable
{
    use Queueable, SerializesModels;

    private $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        //Adicionando os dados do array
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->replyTo($this->data['reply_email'], $this->data['reply_name']) // Quem mandou
            ->to(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')) // De onde sai o email
            ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')) // Pra onde vai o email
            ->subject('Novo Contato: ' . $this->data['reply_name'])
            ->markdown('web.emails.contact', [
                'name' => $this->data['reply_name'],
                'email' => $this->data['reply_email'],
                'cell' => $this->data['cell'],
                'message' => $this->data['message'],
            ]);
    }
}
