<?php

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Subscription;
use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use phpDocumentor\Reflection\Types\This;

class User extends Authenticatable {

	use HasApiTokens, HasFactory, Notifiable, SoftDeletes, Billable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = [''];
//	protected $fillable = [
//		'name',
//		'email',
//		'password',
//	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
	];

    public function sub_tags()
    {
        return $this->hasMany(UserTags::class, 'user_id')->whereNotNull('sub_tag_id');
    }
    public function followers()
    {
        return $this->hasMany(Follower::class, 'following');
    }
    public function best_records()
    {
        return $this->hasMany(Follower::class, 'following');
    }
    public function followings()
    {
        return $this->hasMany(Follower::class, 'follower_id');
    }
    public function tags()
    {
//        dd($user_id);
        return $this->hasMany(UserTags::class, 'user_id')->whereNull('sub_tag_id');
    }
//    public function tags(){
//        $subtags = $this->sub_tags();
//        $tag = $this->tags_all();
//
//        return $tag->merge($subtags);
//
//    }
    public function getTagsAttribute()
    {
        return $this->tags()->where('sub_tag_id',null)->first();
    }

    public function cards()
    {
        return $this->hasMany(Card::class, 'user_id');
    }
    public function defaultCard(){
        return $this->hasOne(Card::class, 'user_id', 'id')->where('is_default','=', true);
    }

    public function notification_setting()
    {
        return $this->hasOne(NotificationSetting::class, 'user_id','id');
    }

    public function docs()
    {
        return $this->hasOne(UserImage::class, 'user_id','id');
    }
    public function stance_id()
    {
        return $this->hasOne(Stance::class, 'id','stance_id');
    }
    public function stance()
    {
        return $this->hasOne(Stance::class, 'id','stance_id');
    }
    public function subscription_plan()
    {
        return $this->hasOne(Subscription::class, 'user_id','id')->where('status_id',13);
    }
    public function plan_counter()
    {
        return $this->hasOne(UserPlanCounter::class, 'user_id','id');
    }
    public function group()
    {
        return $this->belongsTo(Group::class, 'id','made_by');
    }
}
