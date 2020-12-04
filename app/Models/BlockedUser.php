<?php

namespace App\Models;

use App\Models\Interfaces\CustomEvents;
use Illuminate\Database\Eloquent\Model;

class BlockedUser extends Model implements CustomEvents
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'blocked_user_id',
        'phone_number',
        'phone_title'
    ];

    /**
     * Return user who block another
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Return user who was blocked
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function blocked_user()
    {
        return $this->belongsTo(User::class, 'blocked_id');
    }

    /**
     * @param $event_name
     * @return string|null
     */
    public function getCustomEventName($event_name)
    {
        $changed = $this->getChanges();

        if(!empty($changed['deleted_at']) && $event_name == 'updated') {
            $event_name = 'blocked';
        } else {
            $event_name = 'unblocked';
        }

        return $event_name;
    }
}
