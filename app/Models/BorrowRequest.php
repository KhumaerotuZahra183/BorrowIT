<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BorrowRequest extends Model
{
    protected $fillable = [
        'user_id',
        'asset_id',
        'request_date',
        'duration_days',
        'status',
        'approve_date',
        'handover_pic',
        'note',
    ];

    protected $casts = [
        'request_date' => 'date',
        'approve_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
