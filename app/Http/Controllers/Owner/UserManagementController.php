<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $role = $request->get('role');
        $status = $request->get('status');
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        // Base query
        $query = User::query();

        // Filter: Only Admin and Customer (Owner cannot see other Owners)
        $query->whereIn('role', ['admin', 'customer']);

        // Apply filters
        if ($role) {
            $query->where('role', $role);
        }

        if ($status !== null && $status !== '') {
            $query->where('is_active', $status === '1');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        // Get paginated results
        $users = $query->paginate(20)->withQueryString();

        // === STATISTICS ===

        // Total counts
        $totalAdmins = User::where('role', 'admin')->count();
        $activeAdmins = User::where('role', 'admin')->where('is_active', true)->count();
        $inactiveAdmins = User::where('role', 'admin')->where('is_active', false)->count();

        $totalCustomers = User::where('role', 'customer')->count();
        $activeCustomers = User::where('role', 'customer')->where('is_active', true)->count();
        $inactiveCustomers = User::where('role', 'customer')->where('is_active', false)->count();

        // New users this month
        $newUsersThisMonth = User::whereIn('role', ['admin', 'customer'])
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // User activity stats using view_customer_statistics
        $customerStats = DB::table('view_customer_statistics')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        $stats = [
            'total_admins' => $totalAdmins,
            'active_admins' => $activeAdmins,
            'inactive_admins' => $inactiveAdmins,
            'total_customers' => $totalCustomers,
            'active_customers' => $activeCustomers,
            'inactive_customers' => $inactiveCustomers,
            'new_users_this_month' => $newUsersThisMonth,
            'total_users' => $totalAdmins + $totalCustomers,
        ];

        return view('owner.users.index', compact(
            'users',
            'stats',
            'customerStats'
        ));
    }

    /**
     * Display user detail
     */
    public function show(User $user)
    {
        // Owner cannot view other owners
        if ($user->role === 'owner') {
            abort(403, 'Unauthorized access.');
        }

        // Load user orders
        $orders = $user->orders()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // User statistics
        // Menggunakan status: done, confirm, paid dan amount_paid
        $userStats = [
            'total_orders' => $user->orders()->count(),
            'completed_orders' => $user->orders()->whereIn('status', ['done', 'confirm', 'paid'])->count(),
            'pending_orders' => $user->orders()->where('status', 'pending')->count(),
            'cancelled_orders' => $user->orders()->where('status', 'cancel')->count(),
            'total_spent' => $user->orders()->whereIn('status', ['done', 'confirm', 'paid'])->sum('amount_paid'),
            'avg_order_value' => $user->orders()->whereIn('status', ['done', 'confirm', 'paid'])->avg('amount_paid') ?? 0,
            'last_order_date' => $user->orders()->max('created_at'),
        ];

        // Activity timeline (last 10 orders)
        $activityTimeline = $user->orders()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('owner.users.show', compact(
            'user',
            'orders',
            'userStats',
            'activityTimeline'
        ));
    }

    /**
     * Show edit form
     */
    public function edit(User $user)
    {
        // Owner cannot edit other owners
        if ($user->role === 'owner') {
            abort(403, 'Unauthorized access.');
        }

        return view('owner.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        // Owner cannot update other owners
        if ($user->role === 'owner') {
            return redirect()->back()->with('error', 'Tidak dapat mengubah data Owner.');
        }

        // Validation
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'role' => ['required', Rule::in(['admin', 'customer'])],
            'is_active' => ['required', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Update user data
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->phone_number = $validated['phone_number'];
        $user->address = $validated['address'];
        $user->role = $validated['role'];
        $user->is_active = $validated['is_active'];

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('owner.users.show', $user)
            ->with('success', "User {$user->username} berhasil diperbarui.");
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        // Owner cannot deactivate other owners
        if ($user->role === 'owner') {
            return redirect()->back()->with('error', 'Tidak dapat mengubah status Owner.');
        }

        // Toggle status
        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with('success', "User {$user->username} berhasil {$statusText}.");
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        // Owner cannot delete other owners
        if ($user->role === 'owner') {
            return redirect()->back()->with('error', 'Tidak dapat menghapus Owner.');
        }

        // Check if user has orders
        if ($user->orders()->count() > 0) {
            return redirect()->back()->with('error', "User {$user->username} memiliki transaksi dan tidak dapat dihapus. Nonaktifkan saja.");
        }

        $username = $user->username;
        $user->delete();

        return redirect()->route('owner.users.index')
            ->with('success', "User {$username} berhasil dihapus.");
    }
}
