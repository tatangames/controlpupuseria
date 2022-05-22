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
    protected $from;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($codigo, $emailuser, $from)
    {
        $this->codigo = $codigo;
        $this->emailuser = $emailuser;
        $this->from = $from;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('llega aqui 11');
        $email = new SendEmailCodigo($this->codigo, $this->from);
        Mail::to($this->emailuser)->send($email);
    }
}
