@php
    $approval = $record->approval;
    $comments = $record->comments ?? collect();
    $activityLogs = $record->activityLogs ?? collect();
    $approvalStatus = $approval?->status ?? \App\Models\Approval::STATUS_NOT_REQUESTED;
    $approvalBadgeStyles = match ($approvalStatus) {
        \App\Models\Approval::STATUS_APPROVED => 'background:#dcfce7;color:#166534;border:1px solid #86efac;',
        \App\Models\Approval::STATUS_REJECTED => 'background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;',
        \App\Models\Approval::STATUS_PENDING => 'background:#fef3c7;color:#92400e;border:1px solid #fcd34d;',
        default => 'background:#e2e8f0;color:#475569;border:1px solid #cbd5e1;',
    };
    $canRequestApproval = $approvalStatus !== \App\Models\Approval::STATUS_PENDING;
    $canReviewApproval = auth()->user()?->isAdmin() && $approvalStatus === \App\Models\Approval::STATUS_PENDING;
@endphp

<div style="display:grid; grid-template-columns:minmax(0, 1.15fr) minmax(320px, 0.85fr); gap:20px; margin-top:20px;">
    <div style="display:flex; flex-direction:column; gap:20px;">
        <div class="table-card">
            <div class="table-header" style="display:flex; justify-content:space-between; align-items:center;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <i class="fas fa-comments" style="color:#8b0000;"></i>
                    <h3 style="margin:0; color:#333;">Comments</h3>
                </div>
                <span style="font-size:12px; color:#64748b; font-weight:600;">{{ $comments->count() }} total</span>
            </div>
            <div style="padding:18px 20px;">
                <form method="POST" action="{{ route('comments.store') }}" style="margin-bottom:18px;">
                    @csrf
                    <input type="hidden" name="subject_type" value="{{ $subjectType }}">
                    <input type="hidden" name="subject_id" value="{{ $record->id }}">
                    <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:8px;">Add Comment</label>
                    <textarea name="body" class="form-control" rows="4" placeholder="Write an update, note, or discussion here..." required>{{ old('body') }}</textarea>
                    @error('body')
                        <div style="color:#dc2626; font-size:12px; margin-top:6px;">{{ $message }}</div>
                    @enderror
                    <div style="margin-top:12px; display:flex; justify-content:flex-end;">
                        <button type="submit" class="btn-red"><i class="fas fa-paper-plane"></i> Post Comment</button>
                    </div>
                </form>

                @if($comments->isEmpty())
                    <div style="padding:18px; border:1px dashed #cbd5e1; border-radius:12px; text-align:center; color:#64748b; font-size:13px;">
                        No comments yet. This is a good place for updates, notes, or coordination.
                    </div>
                @else
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        @foreach($comments as $comment)
                            <div style="padding:14px 16px; border-radius:14px; border:1px solid rgba(226,232,240,0.92); background:linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(248,250,252,0.94) 100%);">
                                <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:8px; flex-wrap:wrap;">
                                    <div style="font-size:13px; font-weight:700; color:#0f172a;">{{ $comment->user->name ?? 'Unknown User' }}</div>
                                    <div style="font-size:11px; color:#64748b;">{{ $comment->created_at?->format('M d, Y h:i A') }}</div>
                                </div>
                                <div style="font-size:13px; color:#475569; line-height:1.6; white-space:pre-line;">{{ $comment->body }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="table-card">
            <div class="table-header" style="display:flex; justify-content:space-between; align-items:center;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <i class="fas fa-clock-rotate-left" style="color:#8b0000;"></i>
                    <h3 style="margin:0; color:#333;">Activity History</h3>
                </div>
                <span style="font-size:12px; color:#64748b; font-weight:600;">{{ $activityLogs->count() }} entries</span>
            </div>
            <div style="padding:18px 20px;">
                @if($activityLogs->isEmpty())
                    <div style="padding:18px; border:1px dashed #cbd5e1; border-radius:12px; text-align:center; color:#64748b; font-size:13px;">
                        No activity recorded yet for this record.
                    </div>
                @else
                    <div style="position:relative; padding-left:26px;">
                        <div style="position:absolute; left:10px; top:4px; bottom:4px; width:2px; background:#e2e8f0;"></div>
                        @foreach($activityLogs as $log)
                            <div style="position:relative; margin-bottom:16px;">
                                <div style="position:absolute; left:-20px; top:5px; width:12px; height:12px; border-radius:999px; background:linear-gradient(135deg, #2563eb 0%, #dc2626 100%); box-shadow:0 0 0 4px rgba(226,232,240,0.65);"></div>
                                <div style="padding:12px 14px; border-radius:12px; border:1px solid rgba(226,232,240,0.92); background:#fff;">
                                    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:4px;">
                                        <div style="font-size:13px; font-weight:700; color:#0f172a;">{{ $log->title ?? ucwords(str_replace('_', ' ', $log->action)) }}</div>
                                        <div style="font-size:11px; color:#64748b;">{{ $log->created_at?->format('M d, Y h:i A') }}</div>
                                    </div>
                                    <div style="font-size:12px; color:#475569; line-height:1.5;">{{ $log->description ?? 'Activity recorded.' }}</div>
                                    <div style="margin-top:6px; font-size:11px; color:#94a3b8;">By {{ $log->user->name ?? 'System' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div style="display:flex; flex-direction:column; gap:20px;">
        <div class="table-card">
            <div class="table-header" style="display:flex; justify-content:flex-start; align-items:center;">
                <i class="fas fa-user-check" style="margin-right:8px; color:#8b0000;"></i>
                <h3 style="margin:0; color:#333;">Approval Workflow</h3>
            </div>
            <div style="padding:18px 20px;">
                <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; padding:14px 16px; border-radius:14px; background:linear-gradient(90deg, rgba(37,99,235,0.06) 0%, rgba(255,255,255,0.98) 50%, rgba(220,38,38,0.08) 100%); border:1px solid rgba(226,232,240,0.92);">
                    <div>
                        <div style="font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.08em;">Current Status</div>
                        <div style="font-size:14px; font-weight:700; color:#0f172a; margin-top:4px;">{{ ucwords(str_replace('_', ' ', $approvalStatus)) }}</div>
                    </div>
                    <span style="display:inline-flex; align-items:center; padding:7px 12px; border-radius:999px; font-size:12px; font-weight:700; {{ $approvalBadgeStyles }}">
                        {{ strtoupper(str_replace('_', ' ', $approvalStatus)) }}
                    </span>
                </div>

                <div style="margin-top:14px; font-size:12px; color:#475569; line-height:1.6;">
                    <div><strong>Requested By:</strong> {{ $approval?->requester?->name ?? 'Not yet requested' }}</div>
                    <div><strong>Reviewed By:</strong> {{ $approval?->reviewer?->name ?? 'Not yet reviewed' }}</div>
                    <div><strong>Requested At:</strong> {{ $approval?->requested_at?->format('M d, Y h:i A') ?? '—' }}</div>
                    <div><strong>Reviewed At:</strong> {{ $approval?->reviewed_at?->format('M d, Y h:i A') ?? '—' }}</div>
                </div>

                @if($approval?->request_note)
                    <div style="margin-top:14px; padding:12px 14px; border-left:3px solid #2563eb; background:#f8fafc; border-radius:10px;">
                        <div style="font-size:12px; font-weight:700; color:#1e3a8a; margin-bottom:4px;">Request Note</div>
                        <div style="font-size:12px; color:#475569; line-height:1.6; white-space:pre-line;">{{ $approval->request_note }}</div>
                    </div>
                @endif

                @if($approval?->review_note)
                    <div style="margin-top:12px; padding:12px 14px; border-left:3px solid #c0392b; background:#fff7f7; border-radius:10px;">
                        <div style="font-size:12px; font-weight:700; color:#991b1b; margin-bottom:4px;">Review Note</div>
                        <div style="font-size:12px; color:#475569; line-height:1.6; white-space:pre-line;">{{ $approval->review_note }}</div>
                    </div>
                @endif

                <form method="POST" action="{{ route('approvals.request') }}" style="margin-top:18px;">
                    @csrf
                    <input type="hidden" name="subject_type" value="{{ $subjectType }}">
                    <input type="hidden" name="subject_id" value="{{ $record->id }}">
                    <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:8px;">Request Approval</label>
                    <textarea name="request_note" class="form-control" rows="3" placeholder="Add a note for the reviewer if needed..." {{ $canRequestApproval ? '' : 'disabled' }}>{{ old('request_note') }}</textarea>
                    <div style="margin-top:12px; display:flex; justify-content:flex-end;">
                        <button type="submit" class="btn-blue" {{ $canRequestApproval ? '' : 'disabled style=opacity:0.65;cursor:not-allowed;' }}><i class="fas fa-paper-plane"></i> Submit Request</button>
                    </div>
                    @if(!$canRequestApproval)
                        <div style="margin-top:8px; font-size:12px; color:#64748b;">A pending approval request already exists for this record.</div>
                    @endif
                </form>

                @if(auth()->user()?->isAdmin())
                    <form method="POST" action="{{ route('approvals.review') }}" style="margin-top:18px; padding-top:18px; border-top:1px solid rgba(226,232,240,0.92);">
                        @csrf
                        <input type="hidden" name="subject_type" value="{{ $subjectType }}">
                        <input type="hidden" name="subject_id" value="{{ $record->id }}">
                        <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:8px;">Admin Review Note</label>
                        <textarea name="review_note" class="form-control" rows="3" placeholder="Add your review message here..." {{ $canReviewApproval ? '' : 'disabled' }}>{{ old('review_note') }}</textarea>
                        <div style="margin-top:12px; display:flex; gap:10px; justify-content:flex-end; flex-wrap:wrap;">
                            <button type="submit" name="decision" value="rejected" class="btn-gray" {{ $canReviewApproval ? '' : 'disabled style=opacity:0.65;cursor:not-allowed;' }}><i class="fas fa-times-circle"></i> Reject</button>
                            <button type="submit" name="decision" value="approved" class="btn-green" {{ $canReviewApproval ? '' : 'disabled style=opacity:0.65;cursor:not-allowed;' }}><i class="fas fa-check-circle"></i> Approve</button>
                        </div>
                        @if(!$canReviewApproval)
                            <div style="margin-top:8px; font-size:12px; color:#64748b;">Admin review becomes available only when a request is pending.</div>
                        @endif
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
