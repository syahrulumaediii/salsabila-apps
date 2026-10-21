<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskQcReview;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    public const DEFAULT_QC_ITEMS = [
        'Dokumen lengkap sesuai KAK',
        'Perhitungan teknis/struktur sudah diperiksa',
        'Gambar perencanaan (DED) sudah diperiksa',
        'RAB & AHSP sudah diperiksa',
        'Konsistensi antar dokumen (Gambar vs RAB vs Spek)',
        'Catatan revisi sebelumnya sudah ditindaklanjuti',
        'Siap diserahkan kepada klien / PPK',
    ];

    public function index(Request $request): Response
    {
        $mode = $request->query('mode', 'NEED_REVIEW');

        $query = Task::with(['project', 'pic', 'subtasks', 'qcReview']);

        if ($mode === 'NEED_REVIEW') {
            $query->whereIn('status', ['Menunggu Pemeriksaan', 'Revisi']);
        }

        $tasks = $query->get();

        return Inertia::render('Review/Index', [
            'tasks' => $tasks,
            'mode' => $mode,
            'defaultQcItems' => self::DEFAULT_QC_ITEMS,
            'sidebarBadges' => [
                'alerts' => Task::all()->filter(fn ($t) => $t->is_overdue)->count(),
                'today' => Task::where('deadline', Carbon::today()->format('Y-m-d'))->where('status', '!=', 'Selesai')->count(),
                'todos' => Task::where('status', '!=', 'Selesai')->count(),
                'review' => Task::where('status', 'Menunggu Pemeriksaan')->count(),
                'issues' => 0,
            ],
        ]);
    }

    public function toggleQcItem(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'index' => 'required|integer|min:0|max:6',
        ]);

        $qc = TaskQcReview::firstOrCreate(['task_id' => $task->id], [
            'qc_checklist' => [false, false, false, false, false, false, false],
        ]);

        $list = $qc->qc_checklist ?? [false, false, false, false, false, false, false];
        $idx = $validated['index'];
        $list[$idx] = ! ($list[$idx] ?? false);

        $qc->update([
            'qc_checklist' => $list,
            'reviewer_user_id' => $request->user()->id,
            'review_date' => Carbon::today()->format('Y-m-d'),
        ]);

        return back();
    }

    public function approve(Request $request, Task $task): RedirectResponse
    {
        $task->update([
            'status' => 'Selesai',
            'progress' => 100,
        ]);

        $qc = TaskQcReview::firstOrCreate(['task_id' => $task->id]);
        $qc->update([
            'status' => 'Approved',
            'reviewer_user_id' => $request->user()->id,
            'review_date' => Carbon::today()->format('Y-m-d'),
            'qc_notes' => $request->input('qc_notes', $qc->qc_notes),
        ]);

        return back();
    }

    public function reject(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'qc_notes' => 'required|string',
        ]);

        $task->update([
            'status' => 'Revisi',
        ]);

        $qc = TaskQcReview::firstOrCreate(['task_id' => $task->id]);
        $qc->update([
            'status' => 'Revision_Required',
            'reviewer_user_id' => $request->user()->id,
            'review_date' => Carbon::today()->format('Y-m-d'),
            'qc_notes' => $validated['qc_notes'],
        ]);

        return back();
    }
}
