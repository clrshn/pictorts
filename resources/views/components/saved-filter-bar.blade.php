@php
    $currentFilters = collect(request()->query())
        ->except(['page', 'export', 'visible_columns', 'report_title', 'paper_size', 'orientation'])
        ->filter(fn ($value) => $value !== null && $value !== '');
@endphp

<div style="display:flex; flex-direction:column; gap:12px; padding:14px 0 4px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;">
        <div style="display:flex; flex-direction:column; gap:4px;">
            <div style="font-size:12px; font-weight:700; letter-spacing:0.08em; color:#64748b; text-transform:uppercase;">Saved Views & Filters</div>
            <div style="font-size:13px; color:#64748b;">
                {{ $currentFilters->isNotEmpty() ? 'Save the current filter setup for quick reuse.' : 'Save the current default view or create a filter preset.' }}
            </div>
        </div>

        <a href="{{ request()->fullUrlWithQuery(['pinned_only' => request('pinned_only') ? null : 1]) }}"
           class="{{ request('pinned_only') ? 'btn-red' : 'btn-gray' }}"
           style="height:36px; min-width:128px; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0;">
            <i class="fas fa-thumbtack"></i> {{ request('pinned_only') ? 'All Records' : 'Pinned Only' }}
        </a>
    </div>

    <form method="POST" action="{{ route('saved-filters.store') }}" style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        @csrf
        <input type="hidden" name="module" value="{{ $module }}">
        @foreach($currentFilters as $key => $value)
            <input type="hidden" name="filters[{{ $key }}]" value="{{ is_array($value) ? json_encode($value) : $value }}">
        @endforeach
        <input type="text" name="name" class="form-control" placeholder="{{ $currentFilters->isNotEmpty() ? 'Save current filter as...' : 'Save current view as...' }}" style="width:260px; height:36px;" required>
        <button type="submit" class="btn-blue" style="height:36px; min-width:120px; display:inline-flex; align-items:center; justify-content:center;">
            <i class="fas fa-bookmark"></i> Save Filter
        </button>
        @if($currentFilters->isNotEmpty())
            <div style="display:inline-flex; align-items:center; gap:8px; padding:7px 12px; border-radius:999px; background:#eff6ff; color:#1d4ed8; font-size:12px; font-weight:700;">
                <i class="fas fa-sliders-h"></i> {{ $currentFilters->count() }} active filter{{ $currentFilters->count() > 1 ? 's' : '' }}
            </div>
        @else
            <div style="display:inline-flex; align-items:center; gap:8px; padding:7px 12px; border-radius:999px; background:#f8fafc; color:#64748b; font-size:12px; font-weight:700;">
                <i class="fas fa-eye"></i> Default current view
            </div>
        @endif
    </form>

    @if(!empty($savedFilters) && $savedFilters->count())
        <div style="display:flex; flex-wrap:wrap; gap:10px;">
            @foreach($savedFilters as $savedFilter)
                <div style="display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px; border:1px solid rgba(148,163,184,0.28); background:#fff; box-shadow:0 6px 14px rgba(15,23,42,0.06);">
                    <a href="{{ url()->current() . '?' . http_build_query($savedFilter->filters ?? []) }}"
                       style="text-decoration:none; color:#334155; font-size:12px; font-weight:700;">
                        {{ $savedFilter->name }}
                    </a>
                    <form method="POST" action="{{ route('saved-filters.destroy', $savedFilter) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="border:none; background:none; color:#94a3b8; cursor:pointer; font-size:14px; line-height:1;">&times;</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</div>
