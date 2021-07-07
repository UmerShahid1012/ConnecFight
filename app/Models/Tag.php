<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model {

    use HasFactory;

    public function sub_tags()
    {
        return $this->hasMany(SubTag::class, 'tag_id','id');
    }
}
