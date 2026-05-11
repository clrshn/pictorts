@php
    $approval = $record->approval;
    $comments = $record->comments ?? collect();
    $activityLogs = $record->activityLogs ?? collect();
    $commentCount = $comments->sum(fn ($comment) => 1 + $comment->children->count());
    $approvalStatus = $approval?->status ?? \App\Models\Approval::STATUS_NOT_REQUESTED;
    $approvalBadgeStyles = match ($approvalStatus) {
        \App\Models\Approval::STATUS_APPROVED => 'background:#dcfce7;color:#166534;border:1px solid #86efac;',
        \App\Models\Approval::STATUS_REJECTED => 'background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;',
        \App\Models\Approval::STATUS_PENDING => 'background:#fef3c7;color:#92400e;border:1px solid #fcd34d;',
        default => 'background:#e2e8f0;color:#475569;border:1px solid #cbd5e1;',
    };
    $canRequestApproval = $approvalStatus !== \App\Models\Approval::STATUS_PENDING;
    $canReviewApproval = auth()->user()?->isAdmin() && $approvalStatus === \App\Models\Approval::STATUS_PENDING;
    $showChecklist = $subjectType === 'todo' && isset($record->subtasks);
    $showRouting = in_array($subjectType, ['document', 'financial'], true) && isset($record->routes);
    $panelId = preg_replace('/[^A-Za-z0-9\-_]/', '-', $subjectType . '-' . $record->id . '-workflow');
@endphp

<div class="table-card" style="margin-top:20px; overflow:hidden;">
    <div style="background:#8b0000; color:#fff; padding:12px 20px; font-weight:600; font-size:14px; display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <span><i class="fas fa-layer-group"></i> Workflow & Collaboration</span>
        <div style="font-size:12px; color:rgba(255,255,255,0.88);">
            {{ $showChecklist ? $record->completion_percent . '% checklist complete • ' : '' }}{{ $commentCount }} comments • {{ $activityLogs->count() }} activities
        </div>
    </div>

    <div style="padding:18px 20px 20px;">
        <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:18px;">
            @if($showChecklist)
                <button type="button" class="workflow-tab-btn" data-panel="{{ $panelId }}" data-tab="checklist" onclick="toggleWorkflowTab('{{ $panelId }}', 'checklist')" style="padding:9px 14px; border-radius:999px; border:1px solid rgba(148,163,184,0.24); background:#fff7ed; color:#9a3412; font-size:12px; font-weight:700; cursor:pointer;">
                    Checklist
                </button>
            @endif
            <button type="button" class="workflow-tab-btn" data-panel="{{ $panelId }}" data-tab="comments" onclick="toggleWorkflowTab('{{ $panelId }}', 'comments')" style="padding:9px 14px; border-radius:999px; border:1px solid rgba(148,163,184,0.24); background:#ffffff; color:#475569; font-size:12px; font-weight:700; cursor:pointer;">
                Comments
            </button>
            <button type="button" class="workflow-tab-btn" data-panel="{{ $panelId }}" data-tab="approval" onclick="toggleWorkflowTab('{{ $panelId }}', 'approval')" style="padding:9px 14px; border-radius:999px; border:1px solid rgba(148,163,184,0.24); background:#ffffff; color:#475569; font-size:12px; font-weight:700; cursor:pointer;">
                Approval
            </button>
            @if($showRouting)
                <button type="button" class="workflow-tab-btn" data-panel="{{ $panelId }}" data-tab="routing" onclick="toggleWorkflowTab('{{ $panelId }}', 'routing')" style="padding:9px 14px; border-radius:999px; border:1px solid rgba(148,163,184,0.24); background:#ffffff; color:#475569; font-size:12px; font-weight:700; cursor:pointer;">
                    Routing
                </button>
            @endif
            <button type="button" class="workflow-tab-btn" data-panel="{{ $panelId }}" data-tab="activity" onclick="toggleWorkflowTab('{{ $panelId }}', 'activity')" style="padding:9px 14px; border-radius:999px; border:1px solid rgba(148,163,184,0.24); background:#ffffff; color:#475569; font-size:12px; font-weight:700; cursor:pointer;">
                Activity
            </button>
        </div>

        @if($showChecklist)
            <div class="workflow-tab-panel" data-panel="{{ $panelId }}" data-tab="checklist" style="display:block;">
                <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:14px;">
                    <div>
                        <div style="font-size:15px; font-weight:700; color:#1e293b;">Checklist / Subtasks</div>
                        <div style="font-size:12px; color:#64748b;">Break the task into smaller steps and mark them as you go.</div>
                    </div>
                    <div style="font-size:12px; font-weight:700; color:#64748b;">{{ $record->completion_percent }}% complete</div>
                </div>

                <form method="POST" action="{{ route('todos.subtasks.store', $record) }}" style="display:flex; gap:10px; margin-bottom:16px; flex-wrap:wrap;">
                    @csrf
                    <input type="text" name="title" class="form-control" placeholder="Add a subtask..." style="flex:1; min-width:240px;" required>
                    <button type="submit" class="btn-blue"><i class="fas fa-plus"></i> Add Subtask</button>
                </form>

                @if($record->subtasks->isEmpty())
                    <div style="padding:18px; border:1px dashed #cbd5e1; border-radius:12px; text-align:center; color:#64748b; font-size:13px;">
                        No subtasks yet. Add a checklist if this task has smaller steps.
                    </div>
                @else
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        @foreach($record->subtasks as $subtask)
                            <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 14px; border-radius:12px; border:1px solid rgba(226,232,240,0.92); background:{{ $subtask->is_completed ? 'linear-gradient(135deg,#ecfdf5 0%,#f0fdf4 100%)' : '#fff' }};">
                                <form method="POST" action="{{ route('todos.subtasks.update', [$record, $subtask]) }}" style="display:flex; align-items:center; gap:12px; flex:1;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_completed" value="{{ $subtask->is_completed ? 0 : 1 }}">
                                    <button type="submit" style="border:none; background:none; cursor:pointer; color:{{ $subtask->is_completed ? '#16a34a' : '#94a3b8' }}; font-size:18px;">
                                        <i class="fas {{ $subtask->is_completed ? 'fa-circle-check' : 'fa-circle' }}"></i>
                                    </button>
                                    <div style="font-size:13px; color:#334155; text-decoration:{{ $subtask->is_completed ? 'line-through' : 'none' }};">
                                        {{ $subtask->title }}
                                    </div>
                                </form>
                                @if(auth()->user()?->isAdmin())
                                    <form method="POST" action="{{ route('todos.subtasks.destroy', [$record, $subtask]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger" style="padding:6px 10px;"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <div class="workflow-tab-panel" data-panel="{{ $panelId }}" data-tab="comments" style="display:{{ $showChecklist ? 'none' : 'block' }};">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:14px;">
                <div>
                    <div style="font-size:15px; font-weight:700; color:#1e293b;">Comments</div>
                    <div style="font-size:12px; color:#64748b;">Use this space for updates, notes, and coordination.</div>
                </div>
                <div style="font-size:12px; font-weight:700; color:#64748b;">{{ $commentCount }} total</div>
            </div>

            <form method="POST" action="{{ route('comments.store') }}" style="margin-bottom:18px;">
                @csrf
                <input type="hidden" name="subject_type" value="{{ $subjectType }}">
                <input type="hidden" name="subject_id" value="{{ $record->id }}">
                <textarea name="body" class="form-control" rows="4" placeholder="Write an update, note, or discussion here..." required>{{ old('body') }}</textarea>
                <input type="hidden" name="parent_id" value="">
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
                        @include('components.comment-thread-item', [
                            'comment' => $comment,
                            'record' => $record,
                            'subjectType' => $subjectType,
                            'depth' => 0,
                        ])
                    @endforeach
                </div>
            @endif
        </div>

        <div class="workflow-tab-panel" data-panel="{{ $panelId }}" data-tab="approval" style="display:none;">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:14px;">
                <div>
                    <div style="font-size:15px; font-weight:700; color:#1e293b;">Approval Workflow</div>
                    <div style="font-size:12px; color:#64748b;">Submit for review and track the current approval state here.</div>
                </div>
                <span style="display:inline-flex; align-items:center; padding:7px 12px; border-radius:999px; font-size:12px; font-weight:700; {{ $approvalBadgeStyles }}">
                    {{ strtoupper(str_replace('_', ' ', $approvalStatus)) }}
                </span>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:12px; margin-bottom:14px;">
                <div style="padding:12px 14px; border-radius:12px; background:#f8fafc; border:1px solid rgba(226,232,240,0.92); font-size:12px; color:#475569;">
                    <strong>Requested By:</strong> {{ $approval?->requester?->name ?? 'Not yet requested' }}
                </div>
                <div style="padding:12px 14px; border-radius:12px; background:#f8fafc; border:1px solid rgba(226,232,240,0.92); font-size:12px; color:#475569;">
                    <strong>Reviewed By:</strong> {{ $approval?->reviewer?->name ?? 'Not yet reviewed' }}
                </div>
                <div style="padding:12px 14px; border-radius:12px; background:#f8fafc; border:1px solid rgba(226,232,240,0.92); font-size:12px; color:#475569;">
                    <strong>Requested At:</strong> {{ $approval?->requested_at?->format('M d, Y h:i A') ?? '--' }}
                </div>
                <div style="padding:12px 14px; border-radius:12px; background:#f8fafc; border:1px solid rgba(226,232,240,0.92); font-size:12px; color:#475569;">
                    <strong>Reviewed At:</strong> {{ $approval?->reviewed_at?->format('M d, Y h:i A') ?? '--' }}
                </div>
            </div>

            @if($approval?->request_note)
                <div style="margin-bottom:12px; padding:12px 14px; border-left:3px solid #2563eb; background:#f8fafc; border-radius:10px;">
                    <div style="font-size:12px; font-weight:700; color:#1e3a8a; margin-bottom:4px;">Request Note</div>
                    <div style="font-size:12px; color:#475569; line-height:1.6; white-space:pre-line;">{{ $approval->request_note }}</div>
                </div>
            @endif

            @if($approval?->review_note)
                <div style="margin-bottom:12px; padding:12px 14px; border-left:3px solid #c0392b; background:#fff7f7; border-radius:10px;">
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

        @if($showRouting)
            <div class="workflow-tab-panel" data-panel="{{ $panelId }}" data-tab="routing" style="display:none;">
                <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:14px;">
                    <div>
                        <div style="font-size:15px; font-weight:700; color:#1e293b;">Routing History</div>
                        <div style="font-size:12px; color:#64748b;">Track releases, receipts, completions, and internal handoffs here.</div>
                    </div>
                    <div style="font-size:12px; font-weight:700; color:#64748b;">{{ $record->routes->count() }} entries</div>
                </div>

                @if($record->routes->isEmpty())
                    <div style="padding:18px; border:1px dashed #cbd5e1; border-radius:12px; text-align:center; color:#64748b; font-size:13px;">
                        No routing history yet.
                    </div>
                @else
                    <div style="position:relative; padding-left:26px;">
                        <div style="position:absolute; left:10px; top:4px; bottom:4px; width:2px; background:#e2e8f0;"></div>
                        @foreach($record->routes->sortBy('datetime_released') as $route)
                            @php
                                $sameOffice = $route->from_office == $route->to_office;
                                $routeRemarks = (string) ($route->remarks ?? '');
                                $isCreated = str_contains($routeRemarks, 'created');
                                $isCompleted = str_contains($routeRemarks, 'DONE') || str_contains($routeRemarks, 'COMPLETED');
                                $entryColor = $route->datetime_received ? '#27ae60' : '#e67e22';
                            @endphp
                            <div style="position:relative; margin-bottom:16px;">
                                <div style="position:absolute; left:-20px; top:5px; width:12px; height:12px; border-radius:999px; background:{{ $entryColor }}; box-shadow:0 0 0 4px rgba(226,232,240,0.65);"></div>
                                <div style="padding:12px 14px; border-radius:12px; border:1px solid rgba(226,232,240,0.92); background:#fff;">
                                    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:4px;">
                                        <div style="font-size:13px; font-weight:700; color:#0f172a;">
                                            @if($sameOffice)
                                                @if($isCreated)
                                                    {{ $subjectType === 'document' ? 'Document' : 'Financial record' }} created at {{ $route->fromOffice->code ?? '?' }}
                                                @elseif($isCompleted)
                                                    {{ $subjectType === 'document' ? 'Document' : 'Financial record' }} done at {{ $route->fromOffice->code ?? '?' }}
                                                @else
                                                    {{ $subjectType === 'document' ? 'Document' : 'Financial record' }} updated at {{ $route->fromOffice->code ?? '?' }}
                                                @endif
                                            @else
                                                {{ $route->fromOffice->code ?? '?' }} <span style="color:#94a3b8;">→</span> {{ $route->toOffice->code ?? '?' }}
                                            @endif
                                        </div>
                                        <div style="font-size:11px; color:#64748b;">{{ $route->datetime_released?->format('M d, Y h:i A') }}</div>
                                    </div>
                                    <div style="font-size:12px; color:#475569; line-height:1.5;">
                                        @if($sameOffice)
                                            @if($isCreated)
                                                Created by {{ $route->releasedByUser->name ?? 'System' }}
                                            @elseif($isCompleted)
                                                Completed by {{ $route->releasedByUser->name ?? 'System' }}
                                            @else
                                                Updated by {{ $route->releasedByUser->name ?? 'System' }}
                                            @endif
                                        @else
                                            Released by {{ $route->releasedByUser->name ?? 'System' }}
                                            @if($route->datetime_received)
                                                · Received by {{ $route->receivedByUser->name ?? 'System' }} on {{ $route->datetime_received->format('M d, Y h:i A') }}
                                            @endif
                                        @endif
                                    </div>
                                    @if($route->remarks)
                                        <div style="margin-top:6px; font-size:12px; color:#64748b; white-space:pre-line;">{{ $route->remarks }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <div class="workflow-tab-panel" data-panel="{{ $panelId }}" data-tab="activity" style="display:none;">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:14px;">
                <div>
                    <div style="font-size:15px; font-weight:700; color:#1e293b;">Activity History</div>
                    <div style="font-size:12px; color:#64748b;">Track the key actions that happened on this record.</div>
                </div>
                <div style="font-size:12px; font-weight:700; color:#64748b;">{{ $activityLogs->count() }} entries</div>
            </div>

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

<script>
    function toggleReplyForm(id) {
        const form = document.getElementById(id);
        if (!form) {
            return;
        }

        form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
    }

    function toggleWorkflowTab(panelId, tabName) {
        document.querySelectorAll(`.workflow-tab-panel[data-panel="${panelId}"]`).forEach((panel) => {
            panel.style.display = panel.dataset.tab === tabName ? 'block' : 'none';
        });

        document.querySelectorAll(`.workflow-tab-btn[data-panel="${panelId}"]`).forEach((button) => {
            const isActive = button.dataset.tab === tabName;
            button.style.background = isActive ? '#fff7ed' : '#ffffff';
            button.style.color = isActive ? '#9a3412' : '#475569';
            button.style.borderColor = isActive ? 'rgba(234,88,12,0.28)' : 'rgba(148,163,184,0.24)';
            button.style.boxShadow = isActive ? '0 8px 18px rgba(234,88,12,0.10)' : 'none';
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const defaultTab = '{{ $showChecklist ? 'checklist' : 'comments' }}';
        toggleWorkflowTab('{{ $panelId }}', defaultTab);
    });
</script>
