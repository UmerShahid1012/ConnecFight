<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $guarded = [''];

    public function sender(){
        return $this->hasOne(User::class,'id','sender_id');
    }
    public function receiver(){
        return $this->hasOne(User::class,'id','receiver_id');
    }

    public function messages(){
        return $this->hasMany(ChatMessage::class,'chat_id','id');
    }
    public function lastMessage(){
        return $this->hasOne(ChatMessage::class,'id','last_message_id');
    }
    public function getCounterNotification(){
//        return $this->hasOne(Notification::class,'chat_id','id')->where('type','competition')->latest();

        return $this->hasOne(Notification::class,'chat_id','id')->where('type','!=','message')
            ->latest();
    }
}
