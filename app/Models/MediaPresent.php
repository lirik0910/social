<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaPresent extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'media_id',
        'present_id',
        'price'
    ];

    /**
     * Return media associated with media presents
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function media()
    {
        return $this->belongsTo(Media::class, "media_id");
    }

    /**
     * Return present associated with media presents
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function present()
    {
        return $this->belongsTo(Present::class, "present_id")->withTrashed();
    }

    /**
     * Return user who gift present
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}


