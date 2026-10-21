<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    public function index(): Response
    {
        $users = User::with(['tasks.subtasks', 'projects'])->get();

        $members = $users->map(function ($u) {
            $userTasks = $u->tasks;
            $activeCount = $userTasks->where('status', '!=', 'Selesai')->count();
            $doneCount = $userTasks->where('status', 'Selesai')->count();
            $overdueCount = $userTasks->filter(fn ($t) => $t->is_overdue)->count();
            $total = $userTasks->count();
            $onTimeRate = $total > 0 ? round((($total - $overdueCount) / $total) * 100) : 100;

            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role,
                'divisi' => $u->divisi,
                'eval_notes' => $u->eval_notes,
                'total_tasks' => $total,
                'active_tasks' => $activeCount,
                'done_tasks' => $doneCount,
                'overdue_tasks' => $overdueCount,
                'on_time_rate' => $onTimeRate,
                'tasks' => $userTasks->values(),
            ];
        });

        return Inertia::render('Team/Index', [
            'members' => $members,
            'divisions' => [
                'Struktur',
                'Arsitektur',
                'MEP',
                'Estimasi & RAB',
                'Survey & Pengawasan',
                'Administrasi & Kontrak',
            ],
            'sidebarBadges' => Task::getSidebarBadges(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string|max:255',
            'divisi' => 'required|string',
            'eval_notes' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);

        $validated['password'] = Hash::make($validated['password'] ?? 'password');
        User::create($validated);

        return back();
    }

    public function update(Request $request, User $team): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$team->id}",
            'role' => 'required|string|max:255',
            'divisi' => 'required|string',
            'eval_notes' => 'nullable|string',
        ]);

        $team->update($validated);

        return back();
    }

    public function destroy(User $team): RedirectResponse
    {
        if ($team->id === auth()->id()) {
            return back()->withErrors(['message' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
        }

        $team->delete();

        return back();
    }
}
