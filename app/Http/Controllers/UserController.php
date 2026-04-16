<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use App\Support\TableExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $query = User::with('office')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%")
                        ->orWhereHas('office', function ($officeQuery) use ($search) {
                            $officeQuery->where('code', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest();
        $exportMode = $request->get('export');

        if ($exportMode === 'csv') {
            $rows = $query->get()->map(function ($user) {
                return [
                    $user->name,
                    $user->email,
                    $user->office?->code ?? '—',
                    strtoupper($user->role),
                    $user->created_at->format('Y-m-d'),
                ];
            })->all();

            return TableExport::csv('users-report.csv', ['Name', 'Email', 'Office', 'Role', 'Created'], $rows);
        }

        if (in_array($exportMode, ['print', 'pdf'], true)) {
            $availableColumns = [
                'name' => 'Name',
                'email' => 'Email',
                'office' => 'Office',
                'role' => 'Role',
                'created' => 'Created',
            ];

            $rows = $query->get()->map(function ($user) {
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'office' => $user->office?->code ?? '—',
                    'role' => strtoupper($user->role),
                    'created' => $user->created_at->format('M d, Y'),
                ];
            })->all();

            $visibleKeys = TableExport::normalizeVisibleColumns($request->get('visible_columns'), $availableColumns);
            [$headers, $printRows] = TableExport::projectRows($availableColumns, $rows, $visibleKeys);

            $responseMethod = $exportMode === 'pdf' ? 'pdfTable' : 'printTable';

            return TableExport::{$responseMethod}('User Management', $headers, $printRows, [
                'Search' => $request->search ?: 'All users',
            ]);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $offices = Office::ordered()->get();
        return view('users.create', compact('offices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'office_id' => ['required', 'exists:offices,id'],
            'role' => ['required', 'in:admin,user'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'office_id' => $request->office_id,
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $offices = Office::ordered()->get();
        return view('users.edit', compact('user', 'offices'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'office_id' => ['required', 'exists:offices,id'],
            'role' => ['required', 'in:admin,user'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'office_id' => $request->office_id,
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Password reset successfully.');
    }
}
