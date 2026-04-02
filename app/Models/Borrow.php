<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Borrow extends Model
{
    protected $fillable = [
        'borrow_request_id',
        'user_id',
        'asset_id',
        'borrow_date',
        'due_date',
        'status',
        'handover_pic',
        'return_pic',
        'returned_at',
        'overdue_notified_at',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'returned_at' => 'datetime',
        'overdue_notified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(BorrowRequest::class, 'borrow_request_id');
    }
}
