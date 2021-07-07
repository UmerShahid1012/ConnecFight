<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    use HasFactory;
    protected $guarded = [''];
    public function media(){

        return $this->hasMany(FightMedia::class,'fight_id');


    }
    public function checkin(){

        return $this->hasOne(CheckIn::class,'id','check_in_id');

    }

}
