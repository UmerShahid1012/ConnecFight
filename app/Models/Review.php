<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [''];

    public function given_by()
    {
        return $this->hasOne(User::class, 'id','given_by');
    }
    public function given_to()
    {
        return $this->hasOne(User::class, 'id','given_to');
    }
    public function sparring_id()
    {
        return $this->hasOne(Sparring::class, 'id','sparring_id');
    }
    public function fight_id()
    {
        return $this->hasOne(Fight::class, 'id','fight_id');
    }

}
