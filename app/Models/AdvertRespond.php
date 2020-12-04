<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertRespond extends Model
{
    protected $table = 'advert_responds';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function advert()
    {
        return $this->belongsTo(Advert::class);
    }

    public function advert_user()
    {
        return $this->belongsTo(User::class, 'advert_user_id');
    }
}
