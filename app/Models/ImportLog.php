<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportLog extends Model
{
    protected $fillable = [
        'import_id',
        'user_id',
        'type',
        'status',
        'total_rows',
        'processed_rows',
        'inserted_rows',
        'failed_rows',
        'error_message',
        'file_name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function incrementProcessed(): void
    {
        $this->increment('processed_rows');
    }

    public function incrementInserted(): void
    {
        $this->increment('inserted_rows');
    }

    public function incrementFailed(): void
    {
        $this->increment('failed_rows');
    }
}
