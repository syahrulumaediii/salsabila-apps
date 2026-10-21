<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['computed_progress', 'is_overdue', 'is_due_soon'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date:Y-m-d',
            'deadline' => 'date:Y-m-d',
            'weight' => 'integer',
            'manual_progress_mode' => 'boolean',
            'progress' => 'integer',
            'need_decision' => 'boolean',
            'client_follow_up' => 'boolean',
            'last_updated_date' => 'date:Y-m-d',
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

    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class)->orderBy('order', 'asc');
    }

    public function qcReview(): HasOne
    {
        return $this->hasOne(TaskQcReview::class);
    }

    /**
     * Get aggregate sidebar badge counts in a single efficient query.
     *
     * @return array<string, int>
     */
    public static function getSidebarBadges(): array
    {
        $today = Carbon::today()->format('Y-m-d');

        $stats = static::query()
            ->selectRaw("
                COUNT(CASE WHEN status != 'Selesai' AND deadline IS NOT NULL AND deadline < ? THEN 1 END) as alerts,
                COUNT(CASE WHEN status != 'Selesai' AND deadline = ? THEN 1 END) as today,
                COUNT(CASE WHEN status != 'Selesai' THEN 1 END) as todos,
                COUNT(CASE WHEN status = 'Menunggu Pemeriksaan' THEN 1 END) as review
            ", [$today, $today])
            ->first();

        $issueCount = Issue::where('status', '!=', 'Selesai Teratasi')->count();

        return [
            'alerts' => (int) ($stats->alerts ?? 0) + $issueCount,
            'today' => (int) ($stats->today ?? 0),
            'todos' => (int) ($stats->todos ?? 0),
            'review' => (int) ($stats->review ?? 0),
            'issues' => $issueCount,
        ];
    }

    protected function computedProgress(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                if ($this->status === 'Selesai') {
                    return 100;
                }

                if (! $this->manual_progress_mode) {
                    $subtasks = $this->relationLoaded('subtasks') ? $this->subtasks : null;
                    if ($subtasks && $subtasks->isNotEmpty()) {
                        $doneCount = $subtasks->where('is_done', true)->count();

                        return (int) round(($doneCount / $subtasks->count()) * 100);
                    }
                }

                return (int) ($this->progress ?? 0);
            }
        );
    }

    protected function isOverdue(): Attribute
    {
        return Attribute::make(
            get: function (): bool {
                if (! $this->deadline || $this->status === 'Selesai') {
                    return false;
                }

                return Carbon::parse($this->deadline)->isPast() && ! Carbon::parse($this->deadline)->isToday();
            }
        );
    }

    protected function isDueSoon(): Attribute
    {
        return Attribute::make(
            get: function (): bool {
                if (! $this->deadline || $this->status === 'Selesai') {
                    return false;
                }

                $deadline = Carbon::parse($this->deadline);
                $today = Carbon::today();
                $in3Days = Carbon::today()->addDays(3);

                return $deadline->gt($today) && $deadline->lte($in3Days);
            }
        );
    }
}
