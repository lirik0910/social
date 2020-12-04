<?php

namespace App\Models;

use App\Traits\ReflectionTrait;
use Jenssegers\Mongodb\Eloquent\Model;

class GlobalLog extends Model
{
    use ReflectionTrait;

    const ADMIN_SECTION_CHARITY = 1;
    const ADMIN_SECTION_MEDIA = 2;
    const ADMIN_SECTION_PAYMENT = 3;
    const ADMIN_SECTION_PRESENTS = 4;
    const ADMIN_SECTION_REPORTS = 5;
    const ADMIN_SECTION_USERS = 6;
    const ADMIN_SECTION_VERIFICATION = 7;

    protected $connection = 'mongodb';
    protected $collection = 'logs';
    protected $fillable = [
        'mutation',
        'section',
        'data',
        'user_id',
        'user_nickname',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
