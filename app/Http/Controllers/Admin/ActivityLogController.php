<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        if (! class_exists(ActivityLog::class) || ! Schema::hasTable('activity_logs')) {
            $logs = new LengthAwarePaginator([], 0, 40);

            return view('admin.logs.index', compact('logs'));
        }

        $logs = ActivityLog::with('user')
            ->when($request->q, function ($q, $term) {
                $q->where(function ($q) use ($term) {
                    $q->where('action', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                });
            })
            ->when($request->user_id, fn ($q, $id) => $q->where('user_id', $id))
            ->latest()
            ->paginate(40)
            ->withQueryString();

        return view('admin.logs.index', compact('logs'));
    }
}
