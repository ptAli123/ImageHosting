<?php

namespace App\Jobs;

use App\Mail\VarificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class MailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $Details;
    protected $email;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email,$Details)
    {
        $this->email = $email;
        $this->Details = $Details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->email)->send(new VarificationMail($this->Details));
    }
}
