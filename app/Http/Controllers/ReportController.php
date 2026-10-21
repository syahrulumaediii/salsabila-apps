<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        $period = $request->query('period', 'ALL');
        $projectId = $request->query('project_id', 'ALL');
        $picId = $request->query('pic_user_id', 'ALL');

        $query = Task::with(['project', 'pic', 'subtasks']);

        if ($projectId !== 'ALL') {
            $query->where('project_id', $projectId);
        }

        if ($picId !== 'ALL') {
            $query->where('pic_user_id', $picId);
        }

        $today = Carbon::today();
        switch ($period) {
            case 'DAILY':
                $query->whereDate('deadline', $today->format('Y-m-d'));
                break;
            case 'WEEKLY':
                $query->whereBetween('deadline', [$today->copy()->subDays(7)->format('Y-m-d'), $today->copy()->addDays(7)->format('Y-m-d')]);
                break;
            case 'MONTHLY':
                $query->whereMonth('deadline', $today->month)->whereYear('deadline', $today->year);
                break;
            case 'ALL':
            default:
                break;
        }

        $tasks = $query->get();
        $projects = Project::with('pic')->get();
        $users = User::all();
        $billings = Billing::all();

        return Inertia::render('Reports/Index', [
            'tasks' => $tasks,
            'projects' => $projects,
            'users' => $users,
            'filters' => [
                'period' => $period,
                'project_id' => $projectId,
                'pic_user_id' => $picId,
            ],
            'summary' => [
                'total_projects' => $projects->count(),
                'active_projects' => $projects->whereIn('status', ['Aktif Berjalan', 'Menunggu Review Klien'])->count(),
                'total_tasks' => $tasks->count(),
                'completed_tasks' => $tasks->where('status', 'Selesai')->count(),
                'total_receivable' => $billings->sum('remaining_val'),
            ],
            'sidebarBadges' => [
                'alerts' => Task::all()->filter(fn ($t) => $t->is_overdue)->count(),
                'today' => Task::where('deadline', Carbon::today()->format('Y-m-d'))->where('status', '!=', 'Selesai')->count(),
                'todos' => Task::where('status', '!=', 'Selesai')->count(),
                'review' => Task::where('status', 'Menunggu Pemeriksaan')->count(),
                'issues' => 0,
            ],
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $tasks = Task::with(['project', 'pic'])->get();
        $fileName = 'Laporan_Pekerjaan_Salsabila_'.Carbon::today()->format('Y-m-d').'.csv';

        $headers = [
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($tasks) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Kode',
                'Nama Pekerjaan',
                'Proyek',
                'Tahapan',
                'Divisi',
                'PIC Utama',
                'Mulai',
                'Deadline',
                'Prioritas',
                'Status',
                'Progress (%)',
                'Kendala',
                'Tindakan Berikutnya',
            ]);

            foreach ($tasks as $t) {
                fputcsv($file, [
                    $t->code,
                    $t->name,
                    $t->project?->name ?? 'Proyek Umum',
                    $t->stage,
                    $t->divisi,
                    $t->pic?->name ?? '-',
                    $t->start_date ? Carbon::parse($t->start_date)->format('Y-m-d') : '',
                    $t->deadline ? Carbon::parse($t->deadline)->format('Y-m-d') : '',
                    $t->priority,
                    $t->status,
                    $t->computed_progress,
                    $t->obstacle,
                    $t->next_action,
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }
}
