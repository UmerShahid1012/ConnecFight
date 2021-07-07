<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockUser extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [''];

    public function blocked_user()
    {
        return $this->hasOne(User::class, 'id','blocked_user');
    }
    public function blocked_by()
    {
        return $this->hasOne(User::class, 'id','block_by');
    }
}
