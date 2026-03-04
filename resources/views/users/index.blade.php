@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="table-card">
                <div style="background:#333; color:#fff; padding:10px 20px; font-weight:600; font-size:13px; display:flex; justify-content:space-between; align-items:center;">
                    <div><i class="fas fa-users"></i> User Management</div>
                    <div>
                        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Create User
                        </a>
                    </div>
                </div>
                <div style="padding:20px;">
                    @if(session('success'))
                        <div class="alert alert-success" style="margin-bottom:20px;">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger" style="margin-bottom:20px;">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover" style="font-size:13px;">
                            <thead style="background:#f8f9fa;">
                                <tr>
                                    <th>Name</th>
                                    <th>Email (Username)</th>
                                    <th>Office</th>
                                    <th>Role</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td style="font-weight:600;">{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->office?->code ?? '—' }}</td>
                                        <td>
                                            <span class="badge {{ $user->isAdmin() ? 'badge-danger' : 'badge-primary' }}" style="font-size:11px;">
                                                {{ strtoupper($user->role) }}
                                            </span>
                                        </td>
                                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-warning" onclick="document.getElementById('resetPasswordModal{{ $user->id }}').style.display='flex'" title="Reset Password">
                                                    <i class="fas fa-key"></i>
                                                </button>
                                                @if($user->id !== auth()->id())
                                                    <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this user?')" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Reset Password Modal -->
                                    <div class="modal fade" id="resetPasswordModal{{ $user->id }}" tabindex="-1" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Reset Password for {{ $user->name }}</h5>
                                                    <button type="button" class="btn-close" onclick="document.getElementById('resetPasswordModal{{ $user->id }}').style.display='none'">&times;</button>
                                                </div>
                                                <form action="{{ route('users.reset-password', $user) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="alert alert-info">
                                                            <strong>Note:</strong> After resetting, provide the new password to the user.
                                                        </div>
                                                        <div class="form-group">
                                                            <label>New Password <span class="text-danger">*</span></label>
                                                            <input type="password" name="password" class="form-control" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Confirm Password <span class="text-danger">*</span></label>
                                                            <input type="password" name="password_confirmation" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('resetPasswordModal{{ $user->id }}').style.display='none'">Cancel</button>
                                                        <button type="submit" class="btn btn-warning">Reset Password</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No users found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top:20px;">
                        {{ $users->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
