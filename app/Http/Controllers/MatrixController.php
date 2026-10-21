<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MatrixController extends Controller
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
                    ->orWhere('sub_stage', 'like', "%{$search}%");
            });
        }

        if ($request->filled('project_id') && $request->project_id !== 'ALL') {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('stage') && $request->stage !== 'ALL') {
            $query->where('stage', $request->stage);
        }

        if ($request->filled('pic_user_id') && $request->pic_user_id !== 'ALL') {
            $query->where('pic_user_id', $request->pic_user_id);
        }

        if ($request->filled('status') && $request->status !== 'ALL') {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('deadline', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('deadline', '<=', $request->date_to);
        }

        $sort = $request->query('sort', 'group');
        switch ($sort) {
            case 'deadline':
                $query->orderByRaw('CASE WHEN deadline IS NULL THEN 1 ELSE 0 END, deadline ASC');
                break;
            case 'priority':
                $query->orderByRaw("CASE priority WHEN 'Kritis' THEN 1 WHEN 'Tinggi' THEN 2 WHEN 'Sedang' THEN 3 WHEN 'Rendah' THEN 4 ELSE 5 END ASC");
                break;
            case 'progress':
                $query->orderBy('progress', 'desc');
                break;
            case 'group':
            default:
                $query->orderBy('project_id', 'asc')
                    ->orderByRaw("CASE stage WHEN 'A. KONTRAK' THEN 1 WHEN 'B. PERENCANAAN' THEN 2 WHEN 'C. PELAKSANAAN / PENGAWASAN' THEN 3 WHEN 'D. PENAGIHAN' THEN 4 ELSE 5 END ASC");
                break;
        }

        $tasks = $query->get();
        $projects = Project::select('id', 'name', 'code')->get();
        $users = User::select('id', 'name')->get();

        return Inertia::render('Matrix/Index', [
            'tasks' => $tasks,
            'projects' => $projects,
            'users' => $users,
            'filters' => $request->only(['search', 'project_id', 'stage', 'pic_user_id', 'status', 'date_from', 'date_to', 'sort']),
            'sidebarBadges' => Task::getSidebarBadges(),
        ]);
    }
}
