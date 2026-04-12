<x-emails.layouts.app>
    <x-slot name="title">
        {{ $subject ?? 'PICTORTS Notification' }}
    </x-slot>

    <h2 style="color: #1a1a2e; margin-bottom: 20px;">{{ $title ?? 'System Notification' }}</h2>
    
    <p>Hello {{ $user->name }},</p>
    
    <div style="background: linear-gradient(135deg, rgba(192,57,43,0.05) 0%, rgba(41,128,185,0.02) 100%); padding: 20px; border-radius: 8px; border-left: 4px solid #c0392b; margin: 20px 0;">
        <h3 style="color: #c0392b; margin-top: 0;">{{ $alertTitle ?? 'Important Update' }}</h3>
        <p style="margin-bottom: 0;">{{ $message }}</p>
    </div>
    
    @if(isset($actionUrl) && $actionText)
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $actionUrl }}" class="btn">{{ $actionText }}</a>
    </div>
    @endif
    
    @if(isset($details))
    <h4 style="color: #1a1a2e; margin-top: 30px;">Additional Details:</h4>
    <ul style="color: #475569;">
        @foreach($details as $detail)
        <li>{{ $detail }}</li>
        @endforeach
    </ul>
    @endif
    
    <div class="alert">
        <strong>Time:</strong> {{ now()->format('F j, Y, g:i a') }}<br>
        <strong>Reference:</strong> {{ $reference ?? 'N/A' }}
    </div>
    
    <p>Best regards,<br>The PICTORTS Team</p>
</x-emails.layouts.app>
