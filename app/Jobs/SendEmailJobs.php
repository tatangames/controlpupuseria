<?php

namespace App\Jobs;

use App\Mail\SendEmailCodigo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $codigo;
    protected $emailuser;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($codigo, $emailuser)
    {
        $this->codigo = $codigo;
        $this->emailuser = $emailuser;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('llega aqui');
        $email = new SendEmailCodigo($this->codigo);
        Mail::to($this->emailuser)->send($email);
    }
}
