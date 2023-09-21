<?php

namespace App\Jobs\Payment;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\_Payment\Api\Generate\StudentInvoiceController;
use App\Traits\Payment\LogActivity;
use App\Enums\Payment\LogStatus;
use DB;

class GenerateInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, LogActivity;

    /**
     * Create a new job instance.
     */
    protected $student;
    protected $log;

    public function __construct($student = null,$log)
    {
        $this->student = $student;
        $this->log = $log;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if($this->student){
            $test = new StudentInvoiceController;
            $result = $test->storeStudentGenerate($this->student,$this->log->log_id);
        }else{
            $this->updateLogStatus($this->log,LogStatus::Success);
        }
    }


}
