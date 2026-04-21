@php
    $isPinned = $record->pinnedByCurrentUser();
@endphp

<form method="POST" action="{{ $isPinned ? route('pins.destroy') : route('pins.store') }}" style="display:inline;">
    @csrf
    @if($isPinned)
        @method('DELETE')
    @endif
    <input type="hidden" name="subject_type" value="{{ $subjectType }}">
    <input type="hidden" name="subject_id" value="{{ $record->id }}">
    <button type="submit" class="{{ $isPinned ? 'btn-orange' : 'btn-gray' }}">
        <i class="fas fa-thumbtack"></i> {{ $isPinned ? 'Pinned' : 'Pin' }}
    </button>
</form>
