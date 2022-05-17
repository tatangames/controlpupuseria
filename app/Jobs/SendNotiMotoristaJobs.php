<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use OneSignal;
class SendNotiMotoristaJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $titulo;
    protected $mensaje;
    protected $idfcm;

    /**
     *envio de notificacion a motoristas
     *
     * @return void
     */
    public function __construct($titulo, $mensaje, $idfcm)
    {
        $this->titulo = $titulo;
        $this->mensaje = $mensaje;
        $this->idfcm = $idfcm;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        OneSignal::notificacionMotorista($this->titulo, $this->mensaje, $this->idfcm);
    }
}
