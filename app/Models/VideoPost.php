<?php

namespace App\Models;

use App\Models\Traits\HasComments;
use Illuminate\Database\Eloquent\Model;

class VideoPost extends Model
{
    use HasComments;

    protected $fillable = ['title', 'description'];
}
