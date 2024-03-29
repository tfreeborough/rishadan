<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Webpatser\Uuid\Uuid;

class User extends Authenticatable
{
    use Notifiable;
    use \App\Traits\Uuids;

    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'verified'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function addresses()
    {
        return $this->hasMany('App\Models\UserAddress');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function email_verification() 
    {
        return $this->hasOne('App\Models\UserEmailVerification');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function collections()
    {
        return $this->hasMany('App\Models\Collection');
    }
    
    public function cards()
    {
        return $this->hasMany('App\Models\CollectionCard');
    }
}
