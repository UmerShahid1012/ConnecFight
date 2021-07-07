<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparringOffer extends Model
{
    use HasFactory;
    protected $guarded = [''];
    public function sparring(){
        return $this->hasOne(Sparring::class,'id','sparring_id');
    }
    public function status(){
        return $this->hasOne(Status::class,'id','status');
    }
    public function user(){
        return $this->hasOne(User::class,'id','offer_by');
    }

}
