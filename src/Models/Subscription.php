<?php

namespace Kanuu\Laravel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $dates = [
        'cancelled_at'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('kanuu.user_model'));
    }

    /**
     * @param mixed $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('status', '=', 'active');
    }

    /**
     * @return bool
     */
    public function onGracePeriod(): bool
    {
        return $this->cancelled_at
            && $this->cancelled_at->isFuture();
    }
}
