<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NostraJob extends Model
{
    use HasFactory;

    protected $fillable = ['payload', 'status', 'job_id', 'thread_id', 'content'];
}
