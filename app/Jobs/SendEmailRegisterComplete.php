<?php

namespace App\Jobs;

use App\Mail\MailRegisterComplete;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailRegisterComplete implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $code;
    protected $name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data,$code, $name)
    {
        $this->data = $data;
        $this->code = $code;
        $this->name = $name;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \SendGrid\Mail\TypeException
     */
    public function handle()
    {
        try {
            $emailSend = $this->data;
            $contentEmail = new MailRegisterComplete($this->code, $this->name);
            Mail::to($emailSend)->send($contentEmail);
        } catch (Exception $e) {
            Log::error("[SendEmailRegisterComplete][handle] error " . $e->getMessage());
            throw new Exception('[SendEmailRegisterComplete][handle] error ' . $e->getMessage());
        }
    }
}
