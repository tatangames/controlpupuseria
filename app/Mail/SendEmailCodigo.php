<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmailCodigo extends Mailable
{
    use Queueable, SerializesModels;

    public $sujeto = "Recuperación de Contraseña";

    public $codigo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($codigo)
    {
        $this->codigo = $codigo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('eltuncazometapan@gmail.com', 'EL TUNCAZO')
            ->subject($this->sujeto)
            ->view('backend.correos.vistaCorreoCodigo')
            ->with([
                'codigo' => $this->codigo
            ]);
    }
}
