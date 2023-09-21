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

class GenerateBulkInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, LogActivity;

    /**
     * Create a new job instance.
     */
    protected $data;
    protected $from;
    protected $log;

    public function __construct($data,$from,$log)
    {
        $this->data = $data;
        $this->from = $from;
        $this->log = $log;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $test = new StudentInvoiceController;
        $result = $test->storeBulkStudentGenerate($this->data, $this->from,$this->log);
    }
}
