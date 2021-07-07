<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [''];

    public function members()
    {
        return $this->hasMany(GroupMember::class, 'group_id');
    }

    public function made_by()
    {
        return $this->hasOne(User::class, 'id','made_by');
    }
}
