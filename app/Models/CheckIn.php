<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CheckIn extends Model
{
    use HasFactory;
    protected $fillable = [
        'week','day','date','user_id','status','sparring_id'
    ];
    public function status(){

        return $this->hasOne(Status::class,'id','status');

    }
}
