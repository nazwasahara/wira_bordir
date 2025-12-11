<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $adminId = $request->get('admin_id');
        $action = $request->get('action');
        $model = $request->get('model');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $search = $request->get('search');

        $query = ActivityLog::with('user')
            ->whereHas('user', function ($q) {
                $q->where('role', 'admin');
            })
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($adminId) {
            $query->where('user_id', $adminId);
        }

        if ($action) {
            $query->where('action', $action);
        }

        if ($model) {
            $query->where('model', $model);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('username', 'like', "%{$search}%");
                    });
            });
        }

        $logs = $query->paginate(30)->withQueryString();

        // Get admins for filter
        $admins = User::where('role', 'admin')->orderBy('username')->get();

        // Statistics
        $stats = [
            'total_logs' => ActivityLog::whereHas('user', function ($q) {
                $q->where('role', 'admin');
            })->count(),
            'today_logs' => ActivityLog::whereHas('user', function ($q) {
                $q->where('role', 'admin');
            })->whereDate('created_at', Carbon::today())->count(),
            'active_admins' => User::where('role', 'admin')
                ->where('is_active', true)
                ->count(),
            'actions_breakdown' => ActivityLog::whereHas('user', function ($q) {
                $q->where('role', 'admin');
            })
                ->selectRaw('action, COUNT(*) as count')
                ->groupBy('action')
                ->get()
                ->pluck('count', 'action'),
        ];

        // Get unique models and actions for filter
        $models = ActivityLog::distinct()->pluck('model')->sort()->values();
        $actions = ActivityLog::distinct()->pluck('action')->sort()->values();

        return view('owner.activity-logs.index', compact(
            'logs',
            'admins',
            'models',
            'actions',
            'stats'
        ));
    }

    /**
     * Show activity log detail
     */
    public function show($id)
    {
        $activityLog = ActivityLog::with('user')->findOrFail($id);
        return view('owner.activity-logs.show', compact('activityLog'));
    }
}
