<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Billing extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['remaining_val'];

    protected function casts(): array
    {
        return [
            'contract_val' => 'float',
            'invoice_val' => 'float',
            'paid_val' => 'float',
            'submit_date' => 'date:Y-m-d',
            'follow_up_date' => 'date:Y-m-d',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }

    protected function remainingVal(): Attribute
    {
        return Attribute::make(
            get: fn (): float => (float) max(0, ($this->invoice_val ?? 0) - ($this->paid_val ?? 0))
        );
    }
}
