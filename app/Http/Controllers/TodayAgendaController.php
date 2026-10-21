<?php

namespace App\Http\Controllers;

use App\Models\DailyAgenda;
use App\Models\DailyChecklist;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TodayAgendaController extends Controller
{
    public function index(Request $request): Response
    {
        $date = $request->query('date', Carbon::today()->format('Y-m-d'));
        $user = $request->user();

        // Find or create daily agenda for this user & date
        $agenda = DailyAgenda::with('checklists')->firstOrCreate(
            [
                'user_id' => $user->id,
                'agenda_date' => $date,
            ],
            [
                'top_task_ids' => [],
                'eval_q1' => '',
                'eval_q2' => '',
                'eval_q3' => '',
                'eval_q4' => '',
                'eval_q5' => '',
            ]
        );

        $tasks = Task::with(['project', 'pic', 'subtasks', 'qcReview'])->get();

        // Categorized Tasks
        $overdueTasks = $tasks->filter(function ($t) use ($date) {
            return $t->status !== 'Selesai' && $t->deadline && $t->deadline < $date;
        })->values();

        $dueTasks = $tasks->filter(function ($t) use ($date) {
            return $t->status !== 'Selesai' && $t->deadline === $date;
        })->values();

        $qcTasks = $tasks->filter(function ($t) {
            return $t->status === 'Menunggu Pemeriksaan';
        })->values();

        $followUpTasks = $tasks->filter(function ($t) {
            return $t->client_follow_up && $t->status !== 'Selesai';
        })->values();

        // Top 3 selected tasks
        $topTaskIds = $agenda->top_task_ids ?? [];
        $top3Tasks = $tasks->whereIn('id', $topTaskIds)->values();

        return Inertia::render('Today/Index', [
            'selectedDate' => $date,
            'agenda' => $agenda,
            'top3Tasks' => $top3Tasks,
            'allTasks' => $tasks->where('status', '!=', 'Selesai')->values(),
            'projects' => Project::select('id', 'name', 'status')->get(),
            'users' => User::select('id', 'name', 'role', 'divisi')->get(),
            'divisions' => ['Arsitektur', 'Struktur', 'MEP', 'Estimasi & RAB', 'Survey & Pengawasan', 'Administrasi & Kontrak'],
            'categories' => [
                'overdue' => $overdueTasks,
                'dueToday' => $dueTasks,
                'qc' => $qcTasks,
                'followUp' => $followUpTasks,
            ],
            'sidebarBadges' => Task::getSidebarBadges(),
        ]);
    }

    public function updateTop3(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'top_task_ids' => 'required|array|max:3',
        ]);

        $agenda = DailyAgenda::firstOrCreate([
            'user_id' => $request->user()->id,
            'agenda_date' => $validated['date'],
        ]);

        $agenda->update(['top_task_ids' => $validated['top_task_ids']]);

        return back();
    }

    public function addChecklist(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'daily_agenda_id' => 'required|exists:daily_agendas,id',
            'text' => 'required|string|max:255',
        ]);

        DailyChecklist::create([
            'daily_agenda_id' => $validated['daily_agenda_id'],
            'text' => $validated['text'],
            'is_done' => false,
            'order' => DailyChecklist::where('daily_agenda_id', $validated['daily_agenda_id'])->count() + 1,
        ]);

        return back();
    }

    public function toggleChecklist(Request $request, DailyChecklist $checklist): RedirectResponse
    {
        $checklist->update(['is_done' => ! $checklist->is_done]);

        return back();
    }

    public function deleteChecklist(Request $request, DailyChecklist $checklist): RedirectResponse
    {
        $checklist->delete();

        return back();
    }

    public function saveEvaluation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'daily_agenda_id' => 'required|exists:daily_agendas,id',
            'eval_q1' => 'nullable|string',
            'eval_q2' => 'nullable|string',
            'eval_q3' => 'nullable|string',
            'eval_q4' => 'nullable|string',
            'eval_q5' => 'nullable|string',
        ]);

        $agenda = DailyAgenda::findOrFail($validated['daily_agenda_id']);
        $agenda->update($validated);

        return back();
    }
}
