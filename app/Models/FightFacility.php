<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FightFacility extends Model
{
    use HasFactory;
    protected $guarded = [''];
    public function facility_id(){

        return $this->hasOne(Facility::class,'id','facility_id');

    }
}
