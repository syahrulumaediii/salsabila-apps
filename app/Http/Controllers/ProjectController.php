<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectHistoryLog;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(): Response
    {
        $projects = Project::with(['pic', 'tasks.subtasks', 'historyLogs'])->get();
        $users = User::select('id', 'name', 'role')->get();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'users' => $users,
            'sidebarBadges' => [
                'alerts' => Task::all()->filter(fn ($t) => $t->is_overdue)->count(),
                'today' => Task::where('deadline', Carbon::today()->format('Y-m-d'))->where('status', '!=', 'Selesai')->count(),
                'todos' => Task::where('status', '!=', 'Selesai')->count(),
                'review' => Task::where('status', 'Menunggu Pemeriksaan')->count(),
                'issues' => 0,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client' => 'required|string|max:255',
            'contract_no' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'deadline' => 'required|date',
            'pic_user_id' => 'required|exists:users,id',
            'team_members' => 'nullable|string',
            'status' => 'required|string',
            'target_progress' => 'required|integer|min:0|max:100',
            'obstacle' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['code'] = 'P'.(Project::count() + 1);
        $project = Project::create($validated);

        ProjectHistoryLog::create([
            'project_id' => $project->id,
            'log_date' => Carbon::today()->format('Y-m-d'),
            'text' => 'Proyek baru didaftarkan ke sistem kontrol oleh Team Leader.',
        ]);

        return back();
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client' => 'required|string|max:255',
            'contract_no' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'deadline' => 'required|date',
            'pic_user_id' => 'required|exists:users,id',
            'team_members' => 'nullable|string',
            'status' => 'required|string',
            'target_progress' => 'required|integer|min:0|max:100',
            'obstacle' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $project->update($validated);

        return back();
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return back();
    }

    public function addHistory(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'text' => 'required|string|max:500',
            'log_date' => 'nullable|date',
        ]);

        ProjectHistoryLog::create([
            'project_id' => $project->id,
            'log_date' => $validated['log_date'] ?? Carbon::today()->format('Y-m-d'),
            'text' => $validated['text'],
        ]);

        return back();
    }
}
