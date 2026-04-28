@php
    $canDeleteComment = auth()->id() === (int) $comment->user_id || auth()->user()?->isAdmin();
    $replyDepth = $depth ?? 0;
@endphp

<div style="padding:14px 16px; border-radius:14px; border:1px solid rgba(226,232,240,0.92); background:linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(248,250,252,0.94) 100%);">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:8px; flex-wrap:wrap;">
        <div style="font-size:13px; font-weight:700; color:#0f172a;">{{ $comment->user->name ?? 'Unknown User' }}</div>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <div style="font-size:11px; color:#64748b;">{{ $comment->created_at?->format('M d, Y h:i A') }}</div>
            <button type="button" onclick="toggleReplyForm('reply-form-{{ $comment->id }}')" style="border:none; background:transparent; color:#2563eb; font-size:12px; font-weight:700; cursor:pointer;">Reply</button>
            @if($canDeleteComment)
                <form method="POST" action="{{ route('comments.destroy', $comment) }}" onsubmit="return confirm('Delete this comment?');" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="border:none; background:transparent; color:#dc2626; font-size:12px; font-weight:700; cursor:pointer;">Delete</button>
                </form>
            @endif
        </div>
    </div>
    <div style="font-size:13px; color:#475569; line-height:1.6; white-space:pre-line;">{{ $comment->body }}</div>

    <div id="reply-form-{{ $comment->id }}" style="display:none; margin-top:12px; padding-top:12px; border-top:1px solid rgba(226,232,240,0.92);">
        <form method="POST" action="{{ route('comments.store') }}">
            @csrf
            <input type="hidden" name="subject_type" value="{{ $subjectType }}">
            <input type="hidden" name="subject_id" value="{{ $record->id }}">
            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
            <textarea name="body" class="form-control" rows="3" placeholder="Write your reply here..." required></textarea>
            <div style="margin-top:10px; display:flex; justify-content:flex-end;">
                <button type="submit" class="btn-blue"><i class="fas fa-reply"></i> Post Reply</button>
            </div>
        </form>
    </div>

    @if($comment->children->isNotEmpty())
        <div style="margin-top:14px; margin-left:{{ $replyDepth < 1 ? '22px' : '0' }}; display:flex; flex-direction:column; gap:10px; border-left:3px solid rgba(37,99,235,0.12); padding-left:14px;">
            @foreach($comment->children as $childComment)
                @include('components.comment-thread-item', [
                    'comment' => $childComment,
                    'record' => $record,
                    'subjectType' => $subjectType,
                    'depth' => $replyDepth + 1,
                ])
            @endforeach
        </div>
    @endif
</div>
