<x-app-layout>
    <x-slot name="header">
        <h1>Tracking Numbers</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('documents.index') }}">Documents</a> / Tracking Numbers</div>
    </x-slot>

    <div class="table-card">
        <div style="background:#333; color:#fff; padding:10px 20px; font-weight:600; font-size:13px; display:flex; justify-content:space-between; align-items:center;">
            <div><i class="fas fa-qrcode"></i> All Tracking Numbers</div>
            <div>
                <a href="{{ route('documents.create') }}" class="btn-red">
                    <i class="fas fa-plus"></i> Create Document
                </a>
            </div>
        </div>
        <div style="padding:20px;">
            <div style="background:#ffffff; border:1px solid #34495e; border-radius:4px; padding:12px 16px; margin-bottom:20px;">
                <strong>Format:</strong> PICTO-{TYPE}-{YEAR}-{SEQUENCE}<br>
                <small style="color:#b8c5d6;">Each document type has its own numbering sequence that resets yearly.</small>
            </div>

            @if(empty($grouped))
                <div style="text-align:center; padding:40px; color:#b8c5d6;">
                    <i class="fas fa-inbox" style="font-size:48px; margin-bottom:16px;"></i>
                    <p>No documents found. Create your first document to see tracking numbers.</p>
                </div>
            @else
                @foreach($grouped as $type => $years)
                    <div style="margin-bottom:32px;">
                        <h3 style="color:#c0392b; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                            <i class="fas fa-file-alt"></i> {{ $type }}
                        </h3>
                        
                        @foreach($years as $year => $documents)
                            <div style="margin-bottom:20px;">
                                <h4 style="color:#b8c5d6; margin-bottom:12px; font-size:14px;">
                                    Year {{ $year }} ({{ count($documents) }} documents)
                                </h4>
                                
                                <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap:12px;">
                                    @foreach($documents as $doc)
                                        <div style="border:1px solid #34495e; border-radius:4px; padding:12px; background:#2c3e50;">
                                            <div style="font-family:monospace; font-size:13px; font-weight:600; color:#c0392b; margin-bottom:4px;">
                                                {{ $doc->dts_number }}
                                            </div>
                                            <div style="font-size:12px; color:#b8c5d6; margin-bottom:4px;">
                                                {{ Str::limit($doc->subject, 50) }}
                                            </div>
                                            <div style="font-size:11px; color:#7f8c8d;">
                                                {{ $doc->created_at->format('M d, Y h:i A') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>
