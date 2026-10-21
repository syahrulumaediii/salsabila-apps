<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BillingController extends Controller
{
    public function index(): Response
    {
        $billings = Billing::with(['project', 'pic'])->get();
        $projects = Project::select('id', 'name', 'client', 'status')->get();
        $users = User::select('id', 'name')->get();

        $totalContract = $billings->sum('contract_val');
        $totalInvoiced = $billings->sum('invoice_val');
        $totalPaid = $billings->sum('paid_val');
        $totalReceivable = $billings->sum('remaining_val');

        return Inertia::render('Billing/Index', [
            'billings' => $billings,
            'projects' => $projects,
            'users' => $users,
            'summary' => [
                'total_contract' => $totalContract,
                'total_invoiced' => $totalInvoiced,
                'total_paid' => $totalPaid,
                'total_receivable' => $totalReceivable,
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

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'client' => 'required|string|max:255',
            'contract_val' => 'required|numeric|min:0',
            'term' => 'required|string|max:255',
            'invoice_val' => 'required|numeric|min:0',
            'paid_val' => 'required|numeric|min:0',
            'submit_date' => 'nullable|date',
            'doc_status' => 'required|string',
            'pay_status' => 'required|string',
            'pic_user_id' => 'required|exists:users,id',
            'follow_up_date' => 'required|date',
        ]);

        Billing::create($validated);

        return back();
    }

    public function update(Request $request, Billing $billing): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'client' => 'required|string|max:255',
            'contract_val' => 'required|numeric|min:0',
            'term' => 'required|string|max:255',
            'invoice_val' => 'required|numeric|min:0',
            'paid_val' => 'required|numeric|min:0',
            'submit_date' => 'nullable|date',
            'doc_status' => 'required|string',
            'pay_status' => 'required|string',
            'pic_user_id' => 'required|exists:users,id',
            'follow_up_date' => 'required|date',
        ]);

        $billing->update($validated);

        return back();
    }

    public function destroy(Billing $billing): RedirectResponse
    {
        $billing->delete();

        return back();
    }
}
