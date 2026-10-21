<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyChecklist extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_done' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(DailyAgenda::class, 'daily_agenda_id');
    }
}
