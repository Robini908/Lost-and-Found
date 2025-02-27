<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Match Confirmation</title>
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
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        .details {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 6px;
        }
        .detail-row {
            margin: 10px 0;
        }
        .detail-label {
            font-weight: bold;
            color: #495057;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0; color: #2563eb;">Item Match Confirmation</h2>
    </div>

    <div class="content">
        <p>Dear {{ $founderName }},</p>

        <p>We're writing to inform you that someone has identified a potential match for an item you found.</p>

        <div class="details">
            <div class="detail-row">
                <span class="detail-label">Requester Name:</span>
                <span>{{ $requesterName }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Requester Email:</span>
                <span>{{ $requesterEmail }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Item:</span>
                <span>{{ $itemTitle }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Location Found:</span>
                <span>{{ $location }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date Found:</span>
                <span>{{ $dateFound }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Match Score:</span>
                <span>{{ number_format($similarityScore * 100, 0) }}%</span>
            </div>
        </div>

        <p>The requester will contact you separately with more details about their lost item. You can verify their identity using the information provided above.</p>

        <p>Thank you for helping reunite people with their lost items!</p>

        <div class="footer">
            <p>This is an automated confirmation email. Please do not reply to this message.</p>
        </div>
    </div>
</body>
</html>
