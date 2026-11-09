<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Borrowing Report - {{ \Carbon\Carbon::parse($monthYear)->format('F Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0D1A63;
            padding-bottom: 15px;
        }
        .header img {
            max-height: 80px;
            margin-bottom: 10px;
        }
        .header h1 {
            color: #0D1A63;
            font-size: 24px;
            margin: 5px 0;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 16px;
            margin: 5px 0;
            color: #555;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f4f7fe;
            color: #0d1a63;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        .status-borrowed { color: #2563eb; font-weight: bold; }
        .status-returned { color: #16a34a; font-weight: bold; }
        .status-overdue { color: #dc2626; font-weight: bold; }
        
        .type-consumable { color: #7e22ce; font-weight: bold; }
        .type-returnable { color: #0284c7; font-weight: bold; }
        
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0px;
            right: 0px;
            height: 30px;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
        .page-number:after {
            content: counter(page);
        }
        ul {
            margin: 0;
            padding-left: 15px;
        }
    </style>
</head>
<body>

    <div class="header">
        @php
            $logoPath = public_path('images/logo-manajemen-logistic.svg');
            $logoData = '';
            if(file_exists($logoPath)) {
                $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                $data = file_get_contents($logoPath);
                $logoData = 'data:image/svg+xml;base64,' . base64_encode($data);
            }
        @endphp
        @if($logoData)
            <img src="{{ $logoData }}" alt="Logo Manajemen Logistic">
        @endif
        <h1>Manajemen Logistic</h1>
        <h2>Monthly Borrowing Report</h2>
        <p>Period: {{ \Carbon\Carbon::parse($monthYear)->format('F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" style="text-align: center;">No</th>
                <th width="12%">Date</th>
                <th width="15%">Borrow ID</th>
                <th width="15%">Borrower Name</th>
                <th width="12%">Type</th>
                <th width="30%">Items Summary</th>
                <th width="11%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $index => $report)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>
                        @php $type = $report->details->first()?->item->type ?? 'N/A'; @endphp
                        {{ $report->borrow_date->format('d M Y') }}
                        @if(strtolower($type) !== 'consumable' && $report->due_date)
                            <br><span style="color: #666; font-size: 10px;">Due: {{ $report->due_date->format('d M Y') }}</span>
                        @endif
                    </td>
                    <td>{{ $report->borrowing_code }}</td>
                    <td>{{ $report->borrower_name }}</td>
                    <td>
                        <span class="type-{{ strtolower($type) }}">
                            {{ ucfirst($type) }}
                        </span>
                    </td>
                    <td>
                        <ul>
                        @foreach($report->details as $detail)
                            <li>
                                {{ $detail->item->item_name ?? 'Unknown' }} 
                                @if(isset($detail->item->condition))
                                <span style="color: #666; font-size: 10px;">
                                    ({{ ucfirst($detail->item->condition) }})
                                </span>
                                @endif
                                <span style="font-weight: bold;">x{{ $detail->quantity }}</span>
                            </li>
                        @endforeach
                        </ul>
                    </td>
                    <td>
                        @php
                            $isOverdue = $report->status === 'borrowed' && $report->due_date && $report->due_date->isPast();
                            $statusLabel = $report->status;
                            if($isOverdue) {
                                $statusLabel = 'overdue';
                            }
                        @endphp
                        <span class="status-{{ strtolower($statusLabel) }}">
                            {{ ucfirst($statusLabel) }}
                        </span>
                        @if($report->status === 'returned' && $report->returns->isNotEmpty())
                            <br><span style="color: #16a34a; font-size: 10px;">Returned: {{ $report->returns->first()->return_date->format('d M Y') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">No borrowing records found for this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated on: {{ now()->format('d M Y H:i:s') }}
        <span style="float: right;">Page <span class="page-number"></span></span>
    </div>

</body>
</html>
