<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Highlight extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [''];

    public function media(){

        return $this->hasMany(FightMedia::class,'highlights_id');

    }
    public function likes(){

        return $this->hasMany(Like::class,'highlight_id');

    }
    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }
}
