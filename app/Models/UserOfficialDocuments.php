<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOfficialDocuments extends Model
{
    use HasFactory;
    protected $guarded = [''];
    public function sub_tag_id()
    {
        return $this->hasOne(SubTag::class, 'id','sub_tag_id');
    }
}
