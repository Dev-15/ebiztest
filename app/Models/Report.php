<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['name','status','file_path','filters','requested_by'];
    protected $casts = ['filters' => 'array'];
}
