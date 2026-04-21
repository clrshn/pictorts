<x-app-layout>
    <x-slot name="header">
        <h1>Global Search</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / Global Search</div>
    </x-slot>

    <div class="table-card" style="margin-bottom:20px;">
        <div class="table-header" style="display:flex; justify-content:flex-start; align-items:center;">
            <i class="fas fa-magnifying-glass" style="margin-right:8px; color:#8b0000;"></i>
            <h3 style="margin:0; color:#333;">Search Across Modules</h3>
        </div>
        <div style="padding:20px;">
            <form method="GET" action="{{ route('search.index') }}" style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
                <div class="form-group" style="flex:1; min-width:280px; margin:0;">
                    <label>Keyword</label>
                    <input type="text" name="q" class="form-control" value="{{ $query }}" placeholder="Search tasks, documents, DTS numbers, suppliers, and more...">
                </div>
                <button type="submit" class="btn-red"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:20px;">
        <div class="table-card">
            <div class="table-header"><h3 style="margin:0; color:#333;">To-Do Results</h3></div>
            <div style="padding:18px 20px;">
                @forelse($results['todos'] as $todo)
                    <a href="{{ route('todos.show', $todo) }}" style="display:block; padding:12px 14px; text-decoration:none; color:inherit; border:1px solid rgba(226,232,240,0.92); border-radius:12px; background:#fff; margin-bottom:10px;">
                        <div style="font-size:13px; font-weight:700; color:#0f172a;">{{ $todo->title }}</div>
                        <div style="font-size:12px; color:#64748b; margin-top:4px;">{{ $todo->assigned_to ?? 'Unassigned' }} • {{ strtoupper($todo->status) }}</div>
                    </a>
                @empty
                    <div style="color:#64748b; font-size:13px;">No To-Do results found.</div>
                @endforelse
            </div>
        </div>

        <div class="table-card">
            <div class="table-header"><h3 style="margin:0; color:#333;">Document Results</h3></div>
            <div style="padding:18px 20px;">
                @forelse($results['documents'] as $document)
                    <a href="{{ route('documents.show', $document) }}" style="display:block; padding:12px 14px; text-decoration:none; color:inherit; border:1px solid rgba(226,232,240,0.92); border-radius:12px; background:#fff; margin-bottom:10px;">
                        <div style="font-size:13px; font-weight:700; color:#0f172a;">{{ $document->subject ?: $document->particulars ?: $document->dts_number }}</div>
                        <div style="font-size:12px; color:#64748b; margin-top:4px;">{{ $document->dts_number }} @if($document->doc_number) • {{ $document->doc_number }} @endif</div>
                    </a>
                @empty
                    <div style="color:#64748b; font-size:13px;">No document results found.</div>
                @endforelse
            </div>
        </div>

        <div class="table-card">
            <div class="table-header"><h3 style="margin:0; color:#333;">Financial Results</h3></div>
            <div style="padding:18px 20px;">
                @forelse($results['financial'] as $financial)
                    <a href="{{ route('financial.show', $financial) }}" style="display:block; padding:12px 14px; text-decoration:none; color:inherit; border:1px solid rgba(226,232,240,0.92); border-radius:12px; background:#fff; margin-bottom:10px;">
                        <div style="font-size:13px; font-weight:700; color:#0f172a;">{{ $financial->description ?: $financial->type }}</div>
                        <div style="font-size:12px; color:#64748b; margin-top:4px;">{{ $financial->supplier ?? 'No supplier' }} @if($financial->type) • {{ $financial->type }} @endif</div>
                    </a>
                @empty
                    <div style="color:#64748b; font-size:13px;">No financial results found.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
