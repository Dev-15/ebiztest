<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Jobs\GenerateReportJob;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function store(Request $r)
    {
        $r->validate(['name'=>'required']);
        $report = Report::create(['name'=>$r->name,'status'=>'pending','filters'=>$r->filters ?? [],'requested_by'=>$r->user()->id]);
        GenerateReportJob::dispatch($report->id, $report->filters);
        return response()->json(['report_id'=>$report->id,'status_url'=>url('/api/reports/'.$report->id.'/status')],201);
    }

    public function status($id)
    {
        $report = Report::findOrFail($id);
        $data = $report->only(['id','status','file_path','error']);
        if ($report->status === 'done') {
            $data['download_url'] = url('/api/reports/'.$report->id.'/download');
        }
        return response()->json($data);
    }

    public function download($id)
    {
        $report = Report::findOrFail($id);
        if ($report->status !== 'done' || !$report->file_path) abort(404);
        return response()->download(storage_path('app/'.$report->file_path));
    }
}
