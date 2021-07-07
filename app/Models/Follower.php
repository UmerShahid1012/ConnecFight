<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Follower extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [''];

    public function fights(){

//        return $this->hasMany(Fight::class,'id')
//            ->where('defender','following')
//            ->orWhere('challenger','following')
//            ->orWhere('posted_by','following');

    }
    public function follower(){
        return $this->hasOne(User::class,'id','follower_id');
    }
    public function following(){
        return $this->hasOne(User::class,'id','following');
    }
}
