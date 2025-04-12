<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost & Found Items Report</title>
    <style>
        /* Reset and Basic Styling */
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            color: #1f2937;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            font-size: 9pt;
            background-color: #fff;
        }

        /* Set landscape orientation */
        @page {
            size: landscape;
            margin: 15mm 10mm;
        }

        /* Professional header with logo space */
        .header {
            width: 100%;
            padding-bottom: 10px;
            border-bottom: 1px solid #d1d5db;
            margin-bottom: 15px;
        }

        .header-content {
            width: 100%;
            display: table;
        }

        .logo-area {
            display: table-cell;
            vertical-align: middle;
            width: 15%;
        }

        .text-area {
            display: table-cell;
            vertical-align: middle;
            width: 60%;
            text-align: center;
        }

        .meta-area {
            display: table-cell;
            vertical-align: middle;
            width: 25%;
            text-align: right;
            font-size: 8pt;
            color: #6b7280;
        }

        .organization-name {
            font-size: 16pt;
            font-weight: bold;
            color: #1e40af;
            margin: 0;
        }

        .report-title {
            font-size: 12pt;
            font-weight: normal;
            color: #1e40af;
            margin: 5px 0 0 0;
        }

        /* Summary section styling */
        .summary {
            background: #f3f6ff;
            border: 1px solid #dbeafe;
            border-radius: 4px;
            padding: 8px 15px;
            margin-bottom: 15px;
        }

        .summary-title {
            font-size: 10pt;
            font-weight: bold;
            color: #1e40af;
            margin: 0 0 5px 0;
        }

        .summary-stats {
            display: table;
            width: 100%;
        }

        .stat-item {
            display: table-cell;
            text-align: center;
            width: 20%;
        }

        .stat-value {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 2px;
            color: #1e3a8a;
        }

        .stat-label {
            font-size: 7pt;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Table styling */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .main-table th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: bold;
            text-align: left;
            padding: 8px 6px;
            border: 1px solid #e5e7eb;
            font-size: 8pt;
        }

        .main-table td {
            padding: 6px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
            font-size: 8pt;
        }

        .main-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .main-table tr:hover {
            background-color: #f3f6ff;
        }

        /* Column widths */
        .col-id { width: 4%; }
        .col-title { width: 13%; }
        .col-category { width: 9%; }
        .col-status { width: 6%; }
        .col-type { width: 5%; }
        .col-description { width: 18%; }
        .col-location { width: 11%; }
        .col-date { width: 8%; }
        .col-properties { width: 12%; }
        .col-reported { width: 9%; }
        .col-image { width: 5%; }

        /* Status badges */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7pt;
            font-weight: 600;
            border-radius: 10px;
            text-transform: uppercase;
        }

        .badge-lost {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .badge-found {
            background-color: #d1fae5;
            color: #047857;
        }

        .badge-claimed {
            background-color: #dbeafe;
            color: #1d4ed8;
        }

        .badge-returned {
            background-color: #e0e7ff;
            color: #4338ca;
        }

        /* Footer design */
        .footer {
            width: 100%;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 7pt;
            color: #9ca3af;
        }

        .page-number {
            text-align: center;
            font-size: 7pt;
            color: #9ca3af;
            margin-top: 5px;
        }

        /* Image thumbnail */
        .thumbnail {
            width: 30px;
            height: 30px;
            object-fit: cover;
            border-radius: 3px;
            border: 1px solid #e5e7eb;
        }

        /* Truncated text */
        .truncate {
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Section headers */
        .section-title {
            font-size: 11pt;
            color: #1e40af;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }

        /* Handle page breaks */
        .page-break {
            page-break-after: always;
        }

        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="logo-area">
                <!-- Organization logo would go here -->
                <div style="font-weight: bold; font-size: 12pt;">LOST & FOUND</div>
                <div style="font-size: 7pt; color: #6b7280;">OFFICIAL DOCUMENT</div>
            </div>
            <div class="text-area">
                <div class="organization-name">{{ config('app.name', 'Lost & Found System') }}</div>
                <div class="report-title">Inventory of {{ $isAdmin ? 'All' : 'User' }} Items</div>
            </div>
            <div class="meta-area">
                {{ $isAdmin ? 'Administrator Report' : 'User Report: ' . $user->name }}<br>
                Generated: {{ \Carbon\Carbon::parse($generated_at)->format('F j, Y \a\t g:i a') }}<br>
                Ref: LF-{{ substr(md5($generated_at), 0, 8) }}
            </div>
        </div>
    </div>

    <div class="summary">
        <div class="summary-title">Report Summary</div>
        <div class="summary-stats">
            @php
                $lostCount = $items->where('status', 'lost')->count();
                $foundCount = $items->where('status', 'found')->count();
                $claimedCount = $items->where('status', 'claimed')->count();
                $returnedCount = $items->where('status', 'returned')->count();
                $totalCount = $items->count();
            @endphp
            <div class="stat-item">
                <div class="stat-value">{{ $totalCount }}</div>
                <div class="stat-label">Total Items</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $lostCount }}</div>
                <div class="stat-label">Lost</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $foundCount }}</div>
                <div class="stat-label">Found</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $claimedCount }}</div>
                <div class="stat-label">Claimed</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $returnedCount }}</div>
                <div class="stat-label">Returned</div>
            </div>
        </div>
    </div>

    <div class="section-title">Inventory List</div>

    <table class="main-table">
        <thead>
            <tr>
                <th class="col-id">#</th>
                <th class="col-title">Item Title</th>
                <th class="col-category">Category</th>
                <th class="col-status">Status</th>
                <th class="col-type">Type</th>
                <th class="col-description">Description</th>
                <th class="col-location">Location</th>
                <th class="col-date">Date</th>
                <th class="col-properties">Properties</th>
                <th class="col-reported">Reported By</th>
                <th class="col-image">Image</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr class="no-break">
                <td class="col-id">{{ $index + 1 }}</td>
                <td class="col-title">
                    <strong>{{ $item->title }}</strong><br>
                    <span style="font-size: 7pt; color: #6b7280;">ID: {{ substr(md5($item->id), 0, 8) }}</span>
                </td>
                <td class="col-category">{{ $item->category->name ?? 'N/A' }}</td>
                <td class="col-status">
                    <span class="badge badge-{{ $item->status }}">{{ strtoupper($item->status) }}</span>
                </td>
                <td class="col-type">{{ ucfirst($item->item_type) }}</td>
                <td class="col-description">{{ Str::limit($item->description, 100) }}</td>
                <td class="col-location">{{ $item->location_address ?? $item->area ?? 'N/A' }}</td>
                <td class="col-date">
                    @if($item->item_type === 'found')
                        {{ $item->date_found ? $item->date_found->format('M j, Y') : 'N/A' }}
                    @else
                        {{ $item->date_lost ? $item->date_lost->format('M j, Y') : 'N/A' }}
                    @endif
                </td>
                <td class="col-properties">
                    @if($item->brand || $item->model)
                        <strong>Brand/Model:</strong> {{ $item->brand }} {{ $item->model ? '/ ' . $item->model : '' }}<br>
                    @endif
                    @if($item->color)
                        <strong>Color:</strong> {{ $item->color }}<br>
                    @endif
                    @if($item->condition)
                        <strong>Condition:</strong> {{ ucfirst($item->condition) }}<br>
                    @endif
                    @if($item->estimated_value)
                        <strong>Value:</strong> {{ $item->currency }} {{ number_format($item->estimated_value, 2) }}
                    @endif
                </td>
                <td class="col-reported">
                    <div>
                        @if($item->is_anonymous)
                            Anonymous
                        @else
                            {{ $item->user->name ?? 'N/A' }}
                        @endif
                    </div>
                    <div style="font-size: 7pt; color: #6b7280;">
                        {{ $item->created_at->format('M j, Y') }}
                    </div>
                </td>
                <td class="col-image">
                    @if($item->images->isNotEmpty())
                        <img class="thumbnail" src="{{ storage_path('app/public/' . $item->images->first()->image_path) }}" alt="">
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div>Generated by {{ config('app.name', 'Lost & Found System') }} | Document Reference: LF-{{ substr(md5($generated_at), 0, 8) }}</div>
        <div>This document is an official record of lost and found items. For inquiries, contact system administrator.</div>
        <div>&copy; {{ date('Y') }} {{ config('app.name', 'Your Organization') }}. All rights reserved.</div>
    </div>

    <div class="page-number">
        Page [page_num] of [page_total]
    </div>
</body>
</html>
