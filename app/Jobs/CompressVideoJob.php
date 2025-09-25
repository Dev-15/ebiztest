<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Video;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class CompressVideoJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $id;
    public function __construct($id) { $this->id = $id; }
    public function handle()
    {
        $v = Video::find($this->id);
        $v->update(['status'=>'processing']);
        $orig = storage_path('app/'.$v->original_path);
        $outRel = 'videos/compressed/'.uniqid().'.mp4';
        $out = storage_path('app/'.$outRel);
        $ffmpeg = config('app.ffmpeg_binary', '/usr/bin/ffmpeg');
        $cmd = [$ffmpeg, '-y', '-i', $orig, '-vcodec', 'libx264', '-preset', 'slow', '-crf', '28', '-acodec', 'aac', '-b:a', '128k', $out];
        $process = new Process($cmd);
        $process->setTimeout(600);
        $process->run();
        if (!$process->isSuccessful()) {
            $v->update(['status'=>'failed','error'=>$process->getErrorOutput()]);
            Log::error('video.compress.failed', ['id'=>$v->id,'err'=>$process->getErrorOutput()]);
            return;
        }
        $v->update(['status'=>'done','compressed_path'=>$outRel]);
        Log::info('video.compress.done',['id'=>$v->id]);
    }
}
