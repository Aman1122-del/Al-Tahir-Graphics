<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Chat Message - {{ $chatTitle }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #3B82F6;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .content {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 0 0 8px 8px;
        }
        .message-box {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #3B82F6;
            margin: 15px 0;
        }
        .sender-info {
            background: #e5e7eb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .btn {
            display: inline-block;
            background: #3B82F6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Chat Message</h1>
        <p>{{ $chatTitle }}</p>
    </div>
    
    <div class="content">
        <div class="sender-info">
            <strong>From:</strong> {{ $senderName }}<br>
            <strong>Email:</strong> {{ $senderEmail }}<br>
            <strong>Time:</strong> {{ $message->created_at->format('M j, Y g:i A') }}
        </div>
        
        <div class="message-box">
            <h3>Message:</h3>
            <p>{{ $message->message }}</p>
            
            @if($message->hasFile())
                <p><strong>Attachment:</strong> 
                    <a href="{{ $message->file_url }}" target="_blank">{{ $message->file_name }}</a>
                    ({{ $message->file_size_formatted }})
                </p>
            @endif
        </div>
        
        <div style="text-align: center;">
            <a href="{{ url('/admin/chat/' . $chatId) }}" class="btn">
                View Chat in Admin Panel
            </a>
        </div>
    </div>
    
    <div class="footer">
        <p>This is an automated notification from your chat system.</p>
        <p>Please do not reply to this email. Use the admin panel to respond to the customer.</p>
    </div>
</body>
</html>
