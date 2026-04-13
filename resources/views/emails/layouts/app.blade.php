<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'PICTO - RTS Notification' }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border: 1px solid rgba(192,57,43,0.1);
        }
        .header {
            background: linear-gradient(135deg, #c0392b 0%, #8b0000 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        .content {
            padding: 30px;
        }
        .footer {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid rgba(192,57,43,0.1);
            font-size: 12px;
            color: #64748b;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #c0392b 0%, #8b0000 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(192,57,43,0.3);
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(192,57,43,0.4);
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #c0392b;
            background: rgba(192,57,43,0.1);
        }
        .logo {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div style="display: flex; align-items: center; justify-content: center; gap: 12px;">
                <div style="width:40px;height:40px;border-radius:8px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-file-alt" style="color:#fff;font-size:20px;"></i>
                </div>
                <h1>PICTO - RTS</h1>
            </div>
        </div>
        
        <div class="content">
            {{ $slot }}
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} PICTO - Records and Tracking System. All rights reserved.</p>
            <p>Provincial Information and Communications Technology Office</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
