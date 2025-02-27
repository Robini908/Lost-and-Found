<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reward History</title>
    <style>
        body {
            font-family: 'dejavusans';
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #2563eb;
            font-size: 24px;
            margin: 0;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .summary {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .summary-item {
            padding: 10px;
        }
        .summary-item h4 {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        .summary-item p {
            margin: 5px 0 0;
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #f8fafc;
            text-align: left;
            padding: 12px;
            font-size: 14px;
            font-weight: bold;
            color: #666;
            border-bottom: 2px solid #e5e7eb;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }
        .points {
            font-weight: bold;
        }
        .earned {
            color: #059669;
        }
        .converted {
            color: #dc2626;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 2px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reward History</h1>
        <p>{{ $user->name }}</p>
        <p>{{ $dateFrom }} to {{ $dateTo }}</p>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <h4>Total Points Earned</h4>
                <p class="earned">+{{ number_format($totalEarned) }}</p>
            </div>
            <div class="summary-item">
                <h4>Total Points Converted</h4>
                <p class="converted">-{{ number_format($totalConverted) }}</p>
            </div>
            <div class="summary-item">
                <h4>Current Balance</h4>
                <p>{{ number_format($user->reward_points) }}</p>
            </div>
            <div class="summary-item">
                <h4>Worth in {{ $currencySymbol }}</h4>
                <p>{{ $currencySymbol }}{{ number_format($user->reward_points / $conversionRate, 2) }}</p>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Type</th>
                <th>Points</th>
            </tr>
        </thead>
        <tbody>
            @foreach($history as $record)
                <tr>
                    <td>{{ $record['date'] }}</td>
                    <td>{{ $record['description'] }}</td>
                    <td>{{ ucfirst($record['type']) }}</td>
                    <td class="points {{ $record['type'] === 'earned' ? 'earned' : 'converted' }}">
                        {{ $record['type'] === 'earned' ? '+' : '' }}{{ number_format($record['points']) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('F j, Y \a\t g:i a') }}</p>
        <p>{{ config('app.name') }}</p>
    </div>
</body>
</html>
