<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fight extends Model
{
    use HasFactory , SoftDeletes;
    protected $guarded = [''];

    public function defender(){

        return $this->hasOne(User::class,'id','defender');

    }
    public function backup(){

        return $this->hasOne(User::class,'id','backup');

    }
    public function challenger(){

        return $this->hasOne(User::class,'id','challenger');

    }
    public function defender_id(){

        return $this->hasOne(User::class,'id','defender');

    }
    public function challenger_id(){

        return $this->hasOne(User::class,'id','challenger');

    }
    public function winner(){

        return $this->hasOne(User::class,'id','winner_id');

    }
    public function winner_name(){

        return $this->hasOne(User::class,'id','winner_id');

    }
    public function posted_by(){

        return $this->hasOne(User::class,'id','posted_by');

    }
    public function posted_by_id(){

        return $this->hasOne(User::class,'id','posted_by');

    }
    public function status(){

        return $this->hasOne(Status::class,'id','status');

    }
    public function status_id(){

        return $this->hasOne(Status::class,'id','status');

    }
    public function event_id(){

        return $this->hasOne(Event::class,'id','event_id');

    }
    public function event(){

        return $this->hasOne(Event::class,'id','event_id');

    }
    public function dispute(){

        return $this->hasOne(Dispute::class,'fight_id','id');

    }
    public function media(){

        return $this->hasMany(FightMedia::class,'fight_id');

    }
    public function facilities(){

        return $this->hasMany(FightFacility::class,'fight_id');

    }
    public function likes(){

        return $this->hasMany(Like::class,'fight_id');

    }

}
