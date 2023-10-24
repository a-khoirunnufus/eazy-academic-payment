<?php

namespace App\Jobs\Payment;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Payment\StudentInvoice;
use App\Services\Payment\NewStudentInvoice;
use App\Traits\Payment\LogActivity;
use App\Enums\Payment\LogStatus;
use DB;

class GenerateBulkInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, LogActivity;

    /**
     * Create a new job instance.
     */
    protected $data;
    protected $from;
    protected $log;
    protected $is_admission;

    public function __construct($data,$from,$log,$is_admission)
    {
        $this->data = $data;
        $this->from = $from;
        $this->log = $log;
        $this->is_admission = $is_admission;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $studentInvoice = new StudentInvoice;
        if($this->is_admission == 1){
            $studentInvoice = new NewStudentInvoice;
        }
        $result = $studentInvoice->storeBulkStudentGenerate($this->data, $this->from,$this->log);
    }
}
