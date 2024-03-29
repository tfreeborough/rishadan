<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Webpatser\Uuid\Uuid;

class Collection extends Model
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
        'user_id','name','created_at','updated_at'
    ];


    public function cards()
    {
        return $this->hasMany('App\Models\CollectionCard');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

}
