<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sparring extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [''];

    public function media(){
        return $this->hasMany(FightMedia::class,'sparring_id');
    }
    public function likes(){
        return $this->hasMany(Like::class,'sparring_id');
    }
    public function checkins(){
        return $this->hasMany(CheckIn::class,'sparring_id');
    }
    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }
    public function assigned_to(){
        return $this->hasOne(User::class,'id','assigned_to');
    }
    public function assign(){
        return $this->hasOne(User::class,'id','assigned_to');
    }
    public function dispute(){
        return $this->hasOne(Dispute::class,'id','sparring_id');
    }
//    public function user_id(){
//        return $this->hasOne(User::class,'id','user_id');
//    }
    public function event_id(){
        return $this->hasOne(Event::class,'id','event_id');
    }
    public function event(){
        return $this->hasOne(Event::class,'id','event_id');
    }
    public function offers(){
        return $this->hasMany(SparringOffer::class,'sparring_id');
    }
    public function status(){
        return $this->hasOne(Status::class,'id','status');
    }
    public function status_name(){
        return $this->hasOne(Status::class,'id','status');
    }
    public function facilities(){

        return $this->hasMany(FightFacility::class,'sparring_id');

    }
}
