<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_task_with_subtasks_text(): void
    {
        $user = User::factory()->create();

        $project = Project::create([
            'code' => 'P1',
            'name' => 'Proyek Uji',
            'client' => 'Klien Uji',
            'start_date' => '2026-10-01',
            'deadline' => '2026-12-31',
            'pic_user_id' => $user->id,
            'status' => 'Aktif Berjalan',
            'target_progress' => 50,
        ]);

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'name' => 'Contoh Task',
            'project_id' => $project->id,
            'stage' => 'B. PERENCANAAN',
            'sub_stage' => 'Perhitungan struktur',
            'divisi' => 'Arsitektur',
            'pic_user_id' => $user->id,
            'support_team' => 'Hamdi, Hilmi',
            'start_date' => '2026-10-23',
            'deadline' => '2026-10-26',
            'priority' => 'Sedang',
            'status' => 'Belum Dimulai',
            'weight' => 10,
            'manual_progress_mode' => false,
            'progress' => 0,
            'obstacle' => '-',
            'notes' => '-',
            'next_action' => '-',
            'need_decision' => false,
            'client_follow_up' => false,
            'subtasks_text' => "permodelan\nanalisis\nImplementasi",
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('tasks', [
            'name' => 'Contoh Task',
            'project_id' => $project->id,
        ]);

        $task = Task::where('name', 'Contoh Task')->first();
        $this->assertNotNull($task);
        $this->assertCount(3, $task->subtasks);
        $this->assertDatabaseHas('subtasks', [
            'task_id' => $task->id,
            'title' => 'permodelan',
        ]);
        $this->assertDatabaseHas('subtasks', [
            'task_id' => $task->id,
            'title' => 'analisis',
        ]);
        $this->assertDatabaseHas('subtasks', [
            'task_id' => $task->id,
            'title' => 'Implementasi',
        ]);
    }

    public function test_user_can_update_task_with_subtasks_text(): void
    {
        $user = User::factory()->create();

        $project = Project::create([
            'code' => 'P1',
            'name' => 'Proyek Uji',
            'client' => 'Klien Uji',
            'start_date' => '2026-10-01',
            'deadline' => '2026-12-31',
            'pic_user_id' => $user->id,
            'status' => 'Aktif Berjalan',
            'target_progress' => 50,
        ]);

        $task = Task::create([
            'code' => 'T1',
            'name' => 'Task Lama',
            'project_id' => $project->id,
            'stage' => 'B. PERENCANAAN',
            'divisi' => 'Arsitektur',
            'priority' => 'Sedang',
            'status' => 'Belum Dimulai',
            'weight' => 10,
        ]);

        $response = $this->actingAs($user)->put(route('tasks.update', $task), [
            'name' => 'Task Baru',
            'project_id' => $project->id,
            'stage' => 'B. PERENCANAAN',
            'sub_stage' => 'Perhitungan struktur',
            'divisi' => 'Arsitektur',
            'pic_user_id' => $user->id,
            'support_team' => 'Hamdi',
            'start_date' => '2026-10-23',
            'deadline' => '2026-10-26',
            'priority' => 'Tinggi',
            'status' => 'Sedang Dikerjakan',
            'weight' => 15,
            'manual_progress_mode' => false,
            'progress' => 20,
            'obstacle' => null,
            'notes' => null,
            'next_action' => null,
            'need_decision' => false,
            'client_follow_up' => false,
            'subtasks_text' => "subtask 1\nsubtask 2",
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Task Baru',
            'priority' => 'Tinggi',
        ]);

        $this->assertCount(2, $task->fresh()->subtasks);
    }
}
