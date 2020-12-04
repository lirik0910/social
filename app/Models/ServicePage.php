<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePage extends Model
{
    protected $fillable = [
        'title',
        'content',
        'slug',
        'locale',
        'order',
        'status'
    ];
}
