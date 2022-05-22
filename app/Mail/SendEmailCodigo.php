<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmailCodigo extends Mailable
{
    use Queueable, SerializesModels;

    public $codigo;
    public $from;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct($codigo, $from)
    {
        $this->codigo = $codigo;
        $this->from = $from;
    }

    /**
     * compilador
     *
     * @return $this
     */
    public function build()
    {

        $subject = 'Recuperación de contraseña';
        $name = 'El Tuncazo';

        return $this->from($this->from, $name)
            ->subject($subject)
            ->view('backend.correos.vistacorreocodigo')
            ->with([
                'codigo' => $this->codigo
            ]);
    }
}
