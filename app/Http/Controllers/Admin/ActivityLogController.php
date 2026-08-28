<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $this->requirePermission('activity_logs.view');

        $logs = ActivityLog::query()
            ->with('user')
            ->when($request->q, fn ($q, $term) => $q->where('description', 'like', '%'.$term.'%'))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Admin/ActivityLogs/Index', [
            'logs' => $logs,
            'filters' => $request->only('q'),
        ]);
    }
}
