<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    public function upload(Request $r)
    {
        $r->validate(['file'=>'required|file|mimetypes:video/mp4,video/quicktime|max:102400']);
        $file = $r->file('file');
        $orig = $file->store('videos/originals');
        $video = Video::create(['user_id'=>$r->user()->id,'original_path'=>$orig,'status'=>'queued']);
        \App\Jobs\CompressVideoJob::dispatch($video->id);
        return response()->json(['id'=>$video->id,'original'=>$orig],201);
    }
}
