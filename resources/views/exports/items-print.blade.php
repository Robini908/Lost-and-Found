<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Lost & Found Items</title>
    <style>
        /* Reset and Basic Styling */
        body {
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.4;
            color: #1f2937;
            background-color: white;
            margin: 0;
            padding: 20px;
        }

        /* Header styling */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header-left {
            flex: 1;
        }

        .header-center {
            flex: 2;
            text-align: center;
        }

        .header-right {
            flex: 1;
            text-align: right;
        }

        .header h1 {
            color: #1e40af;
            margin: 0 0 5px 0;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0;
            color: #6b7280;
        }

        /* Print controls */
        .print-controls {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px dashed #d1d5db;
        }

        .print-btn {
            background-color: #1e40af;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.2s;
        }

        .print-btn:hover {
            background-color: #1c3879;
        }

        /* Summary section */
        .summary {
            display: flex;
            background-color: #f3f6ff;
            border: 1px solid #dbeafe;
            border-radius: 6px;
            margin-bottom: 20px;
            padding: 15px;
        }

        .summary-block {
            flex: 1;
            text-align: center;
            padding: 10px;
        }

        .summary-value {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin: 5px 0;
        }

        .summary-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Table styling */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            background-color: #f3f4f6;
            text-align: left;
            padding: 12px 10px;
            border: 1px solid #e5e7eb;
            font-weight: 600;
            color: #374151;
        }

        .items-table td {
            padding: 10px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .items-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .section-title {
            font-size: 18px;
            color: #1e40af;
            margin: 30px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        /* Status badges */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 4px;
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

        /* Thumbnail styling */
        .thumbnail {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
        }

        /* Footer styling */
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }

        /* Column widths */
        .col-id { width: 3%; }
        .col-title { width: 14%; }
        .col-category { width: 10%; }
        .col-status { width: 8%; }
        .col-description { width: 20%; }
        .col-location { width: 14%; }
        .col-date { width: 10%; }
        .col-details { width: 14%; }
        .col-image { width: 7%; }

        /* Print-specific styles */
        @media print {
            body {
                padding: 0;
                margin: 0;
            }

            .print-controls {
                display: none;
            }

            @page {
                size: landscape;
                margin: 15mm 10mm;
            }

            .items-table {
                page-break-inside: auto;
            }

            .items-table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .items-table thead {
                display: table-header-group;
            }

            .footer {
                position: fixed;
                bottom: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <strong style="font-size: 18px;">LOST & FOUND</strong>
            <div style="font-size: 12px; color: #6b7280;">OFFICIAL DOCUMENT</div>
        </div>
        <div class="header-center">
            <h1>{{ config('app.name', 'Lost & Found System') }}</h1>
            <p>Inventory of {{ $isAdmin ? 'All' : 'User' }} Items</p>
        </div>
        <div class="header-right">
            <p>{{ $isAdmin ? 'Administrator View' : 'User View: ' . $user->name }}</p>
            <p>Generated: {{ \Carbon\Carbon::parse($generated_at)->format('F j, Y') }}</p>
            <p>Ref: LF-{{ substr(md5($generated_at), 0, 8) }}</p>
        </div>
    </div>

    <div class="print-controls">
        <p><strong>Print Preview</strong></p>
        <p>This page is formatted for printing in landscape orientation. Click the button below when ready to print.</p>
        <button class="print-btn" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" style="display: inline-block; vertical-align: text-bottom; margin-right: 5px;" viewBox="0 0 16 16">
                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
            </svg>
            Print Items
        </button>
    </div>

    <div class="summary">
        @php
            $lostCount = $items->where('status', 'lost')->count();
            $foundCount = $items->where('status', 'found')->count();
            $claimedCount = $items->where('status', 'claimed')->count();
            $returnedCount = $items->where('status', 'returned')->count();
            $totalCount = $items->count();
        @endphp
        <div class="summary-block">
            <div class="summary-value">{{ $totalCount }}</div>
            <div class="summary-label">Total Items</div>
        </div>
        <div class="summary-block">
            <div class="summary-value">{{ $lostCount }}</div>
            <div class="summary-label">Lost</div>
        </div>
        <div class="summary-block">
            <div class="summary-value">{{ $foundCount }}</div>
            <div class="summary-label">Found</div>
        </div>
        <div class="summary-block">
            <div class="summary-value">{{ $claimedCount }}</div>
            <div class="summary-label">Claimed</div>
        </div>
        <div class="summary-block">
            <div class="summary-value">{{ $returnedCount }}</div>
            <div class="summary-label">Returned</div>
        </div>
    </div>

    <div class="section-title">Item Inventory</div>

    <table class="items-table">
        <thead>
            <tr>
                <th class="col-id">#</th>
                <th class="col-title">Item Title</th>
                <th class="col-category">Category</th>
                <th class="col-status">Status</th>
                <th class="col-description">Description</th>
                <th class="col-location">Location</th>
                <th class="col-date">Date</th>
                <th class="col-details">Details</th>
                <th class="col-image">Image</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
                <tr>
                    <td class="col-id">{{ $index + 1 }}</td>
                    <td class="col-title">
                        <strong>{{ $item->title }}</strong>
                        <div style="font-size: 12px; color: #6b7280;">
                            Type: {{ ucfirst($item->item_type) }}<br>
                            ID: {{ substr(md5($item->id), 0, 8) }}
                        </div>
                    </td>
                    <td class="col-category">{{ $item->category->name ?? 'N/A' }}</td>
                    <td class="col-status">
                        <span class="badge badge-{{ $item->status }}">{{ ucfirst($item->status) }}</span>
                    </td>
                    <td class="col-description">{{ Str::limit($item->description, 120) }}</td>
                    <td class="col-location">{{ $item->location_address ?? $item->area ?? 'N/A' }}</td>
                    <td class="col-date">
                        @if($item->item_type === 'found')
                            <strong>Found:</strong> {{ $item->date_found ? $item->date_found->format('M j, Y') : 'N/A' }}
                        @else
                            <strong>Lost:</strong> {{ $item->date_lost ? $item->date_lost->format('M j, Y') : 'N/A' }}
                        @endif
                        <div style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                            Reported: {{ $item->created_at->format('M j, Y') }}
                        </div>
                    </td>
                    <td class="col-details">
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
                            <strong>Value:</strong> {{ $item->currency }} {{ number_format($item->estimated_value, 2) }}<br>
                        @endif
                        <div style="margin-top: 5px; font-size: 12px;">
                            @if($item->is_anonymous)
                                <strong>Reported By:</strong> Anonymous
                            @else
                                <strong>Reported By:</strong> {{ $item->user->name ?? 'N/A' }}
                            @endif
                        </div>
                    </td>
                    <td class="col-image">
                        @if($item->images->isNotEmpty())
                            <img class="thumbnail" src="{{ asset('storage/' . $item->images->first()->image_path) }}" alt="{{ $item->title }}">
                            @if($item->images->count() > 1)
                                <div style="font-size: 10px; color: #6b7280; margin-top: 3px;">+{{ $item->images->count() - 1 }} more</div>
                            @endif
                        @else
                            <div style="color: #9ca3af;">No image</div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated by {{ config('app.name', 'Lost & Found System') }} | Document Reference: LF-{{ substr(md5($generated_at), 0, 8) }}</p>
        <p>This document is an official record of lost and found items. For inquiries, contact system administrator.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'Your Organization') }}. All rights reserved.</p>
    </div>

    <script>
        // Add optional JavaScript for print functionality
        document.addEventListener('DOMContentLoaded', function() {
            // You can add additional print functionality here if needed
        });
    </script>
</body>
</html>
