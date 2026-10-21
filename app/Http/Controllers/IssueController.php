<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IssueController extends Controller
{
    public const CATEGORIES = [
        'Menunggu keputusan Team Leader',
        'Kendala teknis',
        'Kendala administrasi',
        'Kendala komunikasi',
        'Kendala sumber daya',
        'Kendala pembayaran',
        'Menunggu pihak eksternal',
    ];

    public function index(Request $request): Response
    {
        $query = Issue::with(['project', 'reporter']);

        if ($request->filled('category') && $request->category !== 'ALL') {
            $query->where('category', $request->category);
        }

        $issues = $query->orderByRaw("CASE urgency WHEN 'Mendesak (Blokir Progress)' THEN 1 WHEN 'Tinggi (Berpotensi Terlambat)' THEN 2 WHEN 'Sedang' THEN 3 ELSE 4 END ASC")
            ->orderBy('target_date', 'asc')
            ->get();

        $projects = Project::select('id', 'name')->get();
        $users = User::select('id', 'name')->get();

        return Inertia::render('Issues/Index', [
            'issues' => $issues,
            'projects' => $projects,
            'users' => $users,
            'categories' => self::CATEGORIES,
            'filters' => $request->only(['category']),
            'sidebarBadges' => [
                'alerts' => Task::all()->filter(fn ($t) => $t->is_overdue)->count(),
                'today' => Task::where('deadline', Carbon::today()->format('Y-m-d'))->where('status', '!=', 'Selesai')->count(),
                'todos' => Task::where('status', '!=', 'Selesai')->count(),
                'review' => Task::where('status', 'Menunggu Pemeriksaan')->count(),
                'issues' => $issues->where('status', '!=', 'Selesai Teratasi')->count(),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'category' => 'required|string',
            'problem' => 'required|string',
            'reporter_user_id' => 'required|exists:users,id',
            'urgency' => 'required|string',
            'impact' => 'nullable|string',
            'decision_needed' => 'nullable|string',
            'solution' => 'nullable|string',
            'target_date' => 'required|date',
            'status' => 'required|string',
        ]);

        $validated['reported_date'] = Carbon::today()->format('Y-m-d');
        Issue::create($validated);

        return back();
    }

    public function update(Request $request, Issue $issue): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'category' => 'required|string',
            'problem' => 'required|string',
            'reporter_user_id' => 'required|exists:users,id',
            'urgency' => 'required|string',
            'impact' => 'nullable|string',
            'decision_needed' => 'nullable|string',
            'solution' => 'nullable|string',
            'target_date' => 'required|date',
            'status' => 'required|string',
        ]);

        $issue->update($validated);

        return back();
    }

    public function destroy(Issue $issue): RedirectResponse
    {
        $issue->delete();

        return back();
    }
}
