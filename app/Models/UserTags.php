<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTags extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [''];
    public function tag()
    {
        return $this->hasOne(Tag::class, 'id','tag_id');
    }
    public function tag_id()
    {
        return $this->hasOne(Tag::class, 'id','tag_id');
    }
    public function sub_tag()
    {
        return $this->hasOne(SubTag::class, 'id','sub_tag_id');
    }
    public function sub_tag_id()
    {
        return $this->hasMany(SubTag::class, 'id','sub_tag_id');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id','user_id');
    }
    public function sub_tags()
    {
//        dd($this);
        $user = $this;
        return $this->hasMany(UserTags::class, 'tag_id','tag_id')->whereNotNull('sub_tag_id');
    }

    public function sub_tags_1()
    {
        return $this->hasMany(self::class, 'tag_id','tag_id');
    }

}
