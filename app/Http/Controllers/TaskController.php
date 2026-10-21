<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\TaskQcReview;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Task::with(['project', 'pic', 'subtasks', 'qcReview']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('obstacle', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('project_id') && $request->project_id !== 'ALL') {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('divisi') && $request->divisi !== 'ALL') {
            $query->where('divisi', $request->divisi);
        }

        if ($request->filled('pic_user_id') && $request->pic_user_id !== 'ALL') {
            $query->where('pic_user_id', $request->pic_user_id);
        }

        if ($request->filled('status') && $request->status !== 'ALL') {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority') && $request->priority !== 'ALL') {
            $query->where('priority', $request->priority);
        }

        // Sorting
        $sort = $request->query('sort', 'deadline_asc');
        switch ($sort) {
            case 'priority_desc':
                $query->orderByRaw("CASE priority WHEN 'Kritis' THEN 1 WHEN 'Tinggi' THEN 2 WHEN 'Sedang' THEN 3 WHEN 'Rendah' THEN 4 ELSE 5 END ASC");
                break;
            case 'progress_asc':
                $query->orderBy('progress', 'asc');
                break;
            case 'progress_desc':
                $query->orderBy('progress', 'desc');
                break;
            case 'deadline_asc':
            default:
                $query->orderByRaw('CASE WHEN deadline IS NULL THEN 1 ELSE 0 END, deadline ASC');
                break;
        }

        $tasks = $query->get();
        $projects = Project::select('id', 'name', 'code')->get();
        $users = User::select('id', 'name', 'role', 'divisi')->get();

        $divisions = [
            'Struktur',
            'Arsitektur',
            'MEP',
            'Estimasi & RAB',
            'Survey & Pengawasan',
            'Administrasi & Kontrak',
        ];

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'projects' => $projects,
            'users' => $users,
            'divisions' => $divisions,
            'filters' => $request->only(['search', 'project_id', 'divisi', 'pic_user_id', 'status', 'priority', 'sort']),
            'sidebarBadges' => Task::getSidebarBadges(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'stage' => 'required|string',
            'sub_stage' => 'nullable|string',
            'divisi' => 'required|string',
            'pic_user_id' => 'nullable|exists:users,id',
            'support_team' => 'nullable|string',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date',
            'priority' => 'required|string',
            'status' => 'required|string',
            'weight' => 'required|integer|min:1|max:100',
            'manual_progress_mode' => 'boolean',
            'progress' => 'nullable|integer|min:0|max:100',
            'obstacle' => 'nullable|string',
            'notes' => 'nullable|string',
            'next_action' => 'nullable|string',
            'need_decision' => 'boolean',
            'client_follow_up' => 'boolean',
            'subtasks_text' => 'nullable|string',
        ]);

        $validated['last_updated_date'] = Carbon::today()->format('Y-m-d');
        $validated['code'] = 'T'.(Task::count() + 1);

        $task = Task::create($validated);

        // Process Subtasks from text if provided
        if (! empty($validated['subtasks_text'])) {
            $lines = explode("\n", str_replace("\r", '', $validated['subtasks_text']));
            $order = 1;
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (! empty($trimmed)) {
                    Subtask::create([
                        'task_id' => $task->id,
                        'title' => $trimmed,
                        'is_done' => false,
                        'order' => $order++,
                    ]);
                }
            }
        }

        // Initialize QC Review record
        TaskQcReview::create([
            'task_id' => $task->id,
            'qc_checklist' => [false, false, false, false, false, false, false],
            'status' => $task->status === 'Menunggu Pemeriksaan' ? 'Menunggu Pemeriksaan' : 'Draft',
        ]);

        return back();
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'stage' => 'required|string',
            'sub_stage' => 'nullable|string',
            'divisi' => 'required|string',
            'pic_user_id' => 'nullable|exists:users,id',
            'support_team' => 'nullable|string',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date',
            'priority' => 'required|string',
            'status' => 'required|string',
            'weight' => 'required|integer|min:1|max:100',
            'manual_progress_mode' => 'boolean',
            'progress' => 'nullable|integer|min:0|max:100',
            'obstacle' => 'nullable|string',
            'notes' => 'nullable|string',
            'next_action' => 'nullable|string',
            'need_decision' => 'boolean',
            'client_follow_up' => 'boolean',
            'subtasks_text' => 'nullable|string',
        ]);

        $validated['last_updated_date'] = Carbon::today()->format('Y-m-d');
        $task->update($validated);

        // If new subtasks text provided and subtasks currently empty, add them
        if (! empty($validated['subtasks_text']) && $task->subtasks()->count() === 0) {
            $lines = explode("\n", str_replace("\r", '', $validated['subtasks_text']));
            $order = 1;
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (! empty($trimmed)) {
                    Subtask::create([
                        'task_id' => $task->id,
                        'title' => $trimmed,
                        'is_done' => false,
                        'order' => $order++,
                    ]);
                }
            }
        }

        return back();
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return back();
    }

    public function toggleSubtask(Subtask $subtask): RedirectResponse
    {
        $subtask->update(['is_done' => ! $subtask->is_done]);

        // Auto update parent task status if all done
        $task = $subtask->task;
        if (! $task->manual_progress_mode) {
            $allDone = $task->subtasks()->where('is_done', false)->count() === 0;
            if ($allDone && $task->status !== 'Selesai') {
                $task->update(['status' => 'Menunggu Pemeriksaan', 'progress' => 100]);
            }
        }

        return back();
    }

    public function toggleDone(Task $task): RedirectResponse
    {
        if ($task->status === 'Selesai') {
            $task->update([
                'status' => 'Sedang Dikerjakan',
                'progress' => 80,
            ]);
        } else {
            $task->update([
                'status' => 'Selesai',
                'progress' => 100,
            ]);
            $task->subtasks()->update(['is_done' => true]);
        }

        return back();
    }

    public function updateProgress(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'manual_progress_mode' => 'nullable|boolean',
        ]);

        $data = ['progress' => $validated['progress']];
        if (isset($validated['manual_progress_mode'])) {
            $data['manual_progress_mode'] = $validated['manual_progress_mode'];
        }

        if ($validated['progress'] === 100) {
            $data['status'] = 'Menunggu Pemeriksaan';
        }

        $task->update($data);

        return back();
    }
}
