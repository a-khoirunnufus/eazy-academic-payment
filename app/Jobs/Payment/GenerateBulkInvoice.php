<?php

namespace App\Jobs\Payment;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\_Payment\Api\Generate\StudentInvoiceController;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentDetail;
use App\Models\Payment\MasterJob;
use DB;

class GenerateBulkInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $data;
    protected $from;

    public function __construct($data,$from)
    {
        $this->data = $data;
        $this->from = $from;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $log = MasterJob::create([
            'queue' => 'Generate Tagihan',
            'user_id' => 1,
            'status' => 2,
        ]);
        $test = new StudentInvoiceController;
        $result = $test->storeBulkStudentGenerate($this->data, $this->from,$log->mj_id);
        if($result == true){
            $log->status = 1;
            $log->update();
        }
    }
}
