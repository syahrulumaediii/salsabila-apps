<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['weighted_progress'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date:Y-m-d',
            'deadline' => 'date:Y-m-d',
            'target_progress' => 'integer',
        ];
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function historyLogs(): HasMany
    {
        return $this->hasMany(ProjectHistoryLog::class)->orderBy('log_date', 'desc');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    public function billings(): HasMany
    {
        return $this->hasMany(Billing::class);
    }

    /**
     * Calculate Weighted Progress based on task weights and progress.
     */
    protected function weightedProgress(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                $tasks = $this->relationLoaded('tasks') ? $this->tasks : $this->tasks()->with('subtasks')->get();
                if ($tasks->isEmpty()) {
                    return 0;
                }

                $totalWeight = 0;
                $weightedSum = 0;

                foreach ($tasks as $task) {
                    $weight = (int) ($task->weight ?? 10);
                    $progress = $task->computed_progress;
                    $totalWeight += $weight;
                    $weightedSum += ($weight * $progress);
                }

                return $totalWeight > 0 ? (int) round($weightedSum / $totalWeight) : 0;
            }
        );
    }
}
