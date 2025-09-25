<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;
use App\Models\Report;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class GenerateReportJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $reportId;
    protected $filters;

    public function __construct($reportId, $filters = [])
    {
        $this->reportId = $reportId;
        $this->filters = $filters;
    }

    public function handle()
    {
        $report = Report::find($this->reportId);
        $report->update(['status' => 'processing']);
        Log::info('report.job.started', ['id'=>$this->reportId]);

        try {
            $path = storage_path('app/reports/report-' . $this->reportId . '.xlsx');
            $writer = WriterFactory::create(Type::XLSX);
            $writer->openToFile($path);
            $writer->addRow(['id','user_id','amount','type','created_at']);

            Transaction::orderBy('id')->chunkById(1000, function($rows) use ($writer) {
                foreach ($rows as $r) {
                    $writer->addRow([$r->id,$r->user_id,(string)$r->amount,$r->type,$r->created_at]);
                }
            });

            $writer->close();
            $report->update(['status' => 'done','file_path'=>'reports/report-' . $this->reportId . '.xlsx']);
            Log::info('report.job.completed', ['id'=>$this->reportId]);
        } catch (\Throwable $e) {
            $report->update(['status'=>'failed','error'=>$e->getMessage()]);
            Log::error('report.job.failed', ['id'=>$this->reportId,'error'=>$e->getMessage()]);
            throw $e;
        }
    }
}
