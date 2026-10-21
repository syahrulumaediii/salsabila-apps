<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\MatrixController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TodayAgendaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    // 1. Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Agenda Hari Ini
    Route::get('/today', [TodayAgendaController::class, 'index'])->name('today.index');
    Route::post('/today/top3', [TodayAgendaController::class, 'updateTop3'])->name('today.top3');
    Route::post('/today/checklist', [TodayAgendaController::class, 'addChecklist'])->name('today.checklist.add');
    Route::patch('/today/checklist/{checklist}/toggle', [TodayAgendaController::class, 'toggleChecklist'])->name('today.checklist.toggle');
    Route::delete('/today/checklist/{checklist}', [TodayAgendaController::class, 'deleteChecklist'])->name('today.checklist.delete');
    Route::post('/today/evaluation', [TodayAgendaController::class, 'saveEvaluation'])->name('today.evaluation');

    // 3. To-Do List & Tugas
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::patch('/tasks/subtask/{subtask}/toggle', [TaskController::class, 'toggleSubtask'])->name('tasks.subtask.toggle');
    Route::patch('/tasks/{task}/toggle-done', [TaskController::class, 'toggleDone'])->name('tasks.toggleDone');
    Route::patch('/tasks/{task}/progress', [TaskController::class, 'updateProgress'])->name('tasks.progress');

    // 4. Matriks Pekerjaan
    Route::get('/matrix', [MatrixController::class, 'index'])->name('matrix.index');

    // 5. Monitoring Proyek
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::post('/projects/{project}/history', [ProjectController::class, 'addHistory'])->name('projects.history.add');

    // 6. Kontrol Tim & PIC
    Route::get('/team', [TeamController::class, 'index'])->name('team.index');
    Route::post('/team', [TeamController::class, 'store'])->name('team.store');
    Route::put('/team/{team}', [TeamController::class, 'update'])->name('team.update');
    Route::delete('/team/{team}', [TeamController::class, 'destroy'])->name('team.destroy');

    // 7. Pemeriksaan & Revisi (QC Gate)
    Route::get('/review', [ReviewController::class, 'index'])->name('review.index');
    Route::post('/review/{task}/toggle-qc', [ReviewController::class, 'toggleQcItem'])->name('review.toggleQc');
    Route::post('/review/{task}/approve', [ReviewController::class, 'approve'])->name('review.approve');
    Route::post('/review/{task}/reject', [ReviewController::class, 'reject'])->name('review.reject');

    // 8. Kendala & Keputusan
    Route::get('/issues', [IssueController::class, 'index'])->name('issues.index');
    Route::post('/issues', [IssueController::class, 'store'])->name('issues.store');
    Route::put('/issues/{issue}', [IssueController::class, 'update'])->name('issues.update');
    Route::delete('/issues/{issue}', [IssueController::class, 'destroy'])->name('issues.destroy');

    // 9. Penagihan & Termin
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing', [BillingController::class, 'store'])->name('billing.store');
    Route::put('/billing/{billing}', [BillingController::class, 'update'])->name('billing.update');
    Route::delete('/billing/{billing}', [BillingController::class, 'destroy'])->name('billing.destroy');

    // 10. Laporan & Ekspor
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/csv', [ReportController::class, 'exportCsv'])->name('reports.csv');

    // Profile Settings (Breeze default)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
