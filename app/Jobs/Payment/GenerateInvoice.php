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
use App\Models\Payment\MasterJobDetail;
use App\Models\Payment\PaymentDetail;
use DB;

class GenerateInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $student;
    protected $mj_id;

    public function __construct($student,$mj_id)
    {
        $this->student = $student;
        $this->mj_id = $mj_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $log = MasterJobDetail::create([
            'title' => $this->student->fullname.' - '.$this->student->student_id,
            'mj_id' => $this->mj_id,
            'status' => 2,
        ]);
        $test = new StudentInvoiceController;
        $result = $test->storeStudentGenerate($this->student);

        $log->status = 1;
        $log->update();
    }


}
