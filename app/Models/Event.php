<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory , SoftDeletes;
    protected $guarded = [''];
    public function sub_tag()
    {
        return $this->hasOne(SubTag::class, 'id','sub_tag_id');
    }
}
