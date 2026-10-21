<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $today = Carbon::today()->format('Y-m-d');

        // All tasks with project, pic, subtasks, qcReview
        $tasks = Task::with(['project', 'pic', 'subtasks', 'qcReview'])->get();
        $projects = Project::with(['pic', 'tasks.subtasks', 'historyLogs'])->get();
        $billings = Billing::with(['project', 'pic'])->get();
        $issues = Issue::with(['project', 'reporter'])->get();
        $users = User::all();

        // 8 Stat Cards Calculations
        $activeProjectsCount = $projects->whereIn('status', ['Aktif Berjalan', 'Menunggu Review Klien'])->count();
        $activeTasksCount = $tasks->where('status', '!=', 'Selesai')->count();
        $waitingQcCount = $tasks->where('status', 'Menunggu Pemeriksaan')->count();
        $delayedProjectsCount = $projects->where('status', 'Terhambat')->count();

        $overdueTasksCount = $tasks->filter(fn ($t) => $t->is_overdue)->count();
        $dueTodayTasksCount = $tasks->filter(fn ($t) => $t->status !== 'Selesai' && $t->deadline && Carbon::parse($t->deadline)->isToday())->count();

        $totalReceivables = $billings->sum('remaining_val');
        $totalMembersCount = $users->count();

        // Automated Radar Warnings
        $warnings = [];
        if ($overdueTasksCount > 0) {
            $warnings[] = [
                'type' => 'danger',
                'icon' => 'fa-clock',
                'title' => "{$overdueTasksCount} Tugas Terlambat",
                'desc' => 'Tugas telah melewati tanggal batas waktu penyelesaian.',
                'link' => 'tasks.index',
            ];
        }
        if ($dueTodayTasksCount > 0) {
            $warnings[] = [
                'type' => 'warning',
                'icon' => 'fa-calendar-day',
                'title' => "{$dueTodayTasksCount} Deadline Hari Ini",
                'desc' => 'Wajib dituntaskan atau dimonitor sebelum akhir hari kerja.',
                'link' => 'today.index',
            ];
        }
        if ($waitingQcCount > 0) {
            $warnings[] = [
                'type' => 'purple',
                'icon' => 'fa-clipboard-check',
                'title' => "{$waitingQcCount} Menunggu Pemeriksaan (QC)",
                'desc' => 'Periksa dan setujui di Meja QC Gate.',
                'link' => 'review.index',
            ];
        }
        $pendingIssuesCount = $issues->whereIn('status', ['Menunggu Keputusan TL', 'Sedang Ditindaklanjuti'])->count();
        if ($pendingIssuesCount > 0) {
            $warnings[] = [
                'type' => 'danger',
                'icon' => 'fa-triangle-exclamation',
                'title' => "{$pendingIssuesCount} Kendala & Keputusan TL",
                'desc' => 'Ada kendala teknis atau lapangan yang memerlukan arahan.',
                'link' => 'issues.index',
            ];
        }
        $lateBillingsCount = $billings->where('pay_status', 'Terlambat Cair (Perlu Follow-Up)')->count();
        if ($lateBillingsCount > 0) {
            $warnings[] = [
                'type' => 'danger',
                'icon' => 'fa-file-invoice-dollar',
                'title' => "{$lateBillingsCount} Tagihan Terlambat Cair",
                'desc' => 'Perlu kunjungan follow-up ke bendahara atau instansi.',
                'link' => 'billing.index',
            ];
        }

        // Perhatian Team Leader (TL Attention Tasks)
        $attentionTasks = $tasks->filter(function ($t) {
            if ($t->status === 'Selesai') {
                return false;
            }

            return $t->is_overdue
                || ($t->deadline && Carbon::parse($t->deadline)->isToday())
                || $t->status === 'Menunggu Pemeriksaan'
                || $t->status === 'Terhambat'
                || $t->need_decision;
        })->values();

        // Workload per PIC
        $picWorkload = $users->map(function ($u) use ($tasks) {
            $userTasks = $tasks->where('pic_user_id', $u->id);
            $active = $userTasks->where('status', '!=', 'Selesai')->count();
            $done = $userTasks->where('status', 'Selesai')->count();
            $overdue = $userTasks->filter(fn ($t) => $t->is_overdue)->count();

            return [
                'id' => $u->id,
                'name' => $u->name,
                'role' => $u->role,
                'divisi' => $u->divisi,
                'total_tasks' => $userTasks->count(),
                'active_tasks' => $active,
                'done_tasks' => $done,
                'overdue_tasks' => $overdue,
            ];
        });

        // Global sidebar badges
        $sidebarBadges = [
            'alerts' => $overdueTasksCount + $pendingIssuesCount,
            'today' => $dueTodayTasksCount,
            'todos' => $activeTasksCount,
            'review' => $waitingQcCount,
            'issues' => $pendingIssuesCount,
        ];

        return Inertia::render('Dashboard/Index', [
            'stats' => [
                'active_projects' => $activeProjectsCount,
                'active_tasks' => $activeTasksCount,
                'waiting_qc' => $waitingQcCount,
                'delayed_projects' => $delayedProjectsCount,
                'overdue_tasks' => $overdueTasksCount,
                'due_today_tasks' => $dueTodayTasksCount,
                'total_receivables' => $totalReceivables,
                'total_members' => $totalMembersCount,
            ],
            'warnings' => $warnings,
            'attentionTasks' => $attentionTasks,
            'projects' => $projects,
            'users' => $users->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'role' => $u->role,
                'divisi' => $u->divisi,
            ]),
            'picWorkload' => $picWorkload,
            'divisions' => ['Arsitektur', 'Struktur', 'MEP', 'Estimasi & RAB', 'Survey & Pengawasan', 'Administrasi & Kontrak'],
            'sidebarBadges' => $sidebarBadges,
        ]);
    }
}
