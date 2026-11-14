<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user')->latest();

        if ($user = $request->query('user')) {
            $query->where('user_id', $user);
        }
        if ($action = $request->query('action')) {
            $query->where('action', 'like', $action.'%');
        }

        return view('admin.activity.index', [
            'logs' => $query->paginate(30)->withQueryString(),
            'users' => User::whereNotNull('role_id')->orderBy('name')->get(),
            'actions' => ActivityLog::query()->select('action')->distinct()->pluck('action'),
            'filters' => $request->only('user', 'action'),
        ]);
    }
}
