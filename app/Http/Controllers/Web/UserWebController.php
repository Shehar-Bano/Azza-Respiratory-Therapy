<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserWebController extends Controller
{
    /**
     * Display listing of users with global search and column sorting.
     */
    public function index(Request $request): View
    {
        $query = User::query();

        // Global Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = strtolower($request->input('sort_order', 'desc')) === 'asc' ? 'asc' : 'desc';

        $allowedSorts = ['id', 'name', 'email', 'role', 'status', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->latest();
        }

        $users = $query->paginate(10)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'search' => $request->input('search', ''),
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }
}
