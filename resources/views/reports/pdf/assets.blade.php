<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Assets Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        .meta { color: #666; font-size: 9px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #1f2937; color: #fff; }
        th, td { border: 1px solid #d1d5db; padding: 4px 6px; text-align: left; vertical-align: top; }
        th { font-size: 9px; text-transform: uppercase; letter-spacing: .5px; }
        tr:nth-child(even) td { background: #f9fafb; }
        .pending { color: #92400e; font-weight: bold; }
        .yes { color: #166534; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Asset Inventory Report</h1>
    <div class="meta">
        Generated: {{ $generatedAt->format('Y-m-d H:i') }} ·
        Filters: {{ collect($filters)->filter()->map(fn($v, $k) => "$k=$v")->join(', ') ?: 'none' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Asset Tag</th>
                <th>Company</th>
                <th>Category</th>
                <th>Model</th>
                <th>Assigned User</th>
                <th>Location</th>
                <th>RustDesk</th>
                <th>Signed</th>
                <th>Snipe-IT</th>
                <th>Date Issued</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($assets as $a)
                <tr>
                    <td>{{ $a->asset_tag }}</td>
                    <td>{{ $a->company?->name }}</td>
                    <td>{{ $a->category?->name }}</td>
                    <td>{{ $a->model?->name }}</td>
                    <td>{{ $a->assignee?->name }}</td>
                    <td>{{ $a->location?->name }}</td>
                    <td>{{ $a->rustdesk_id }}</td>
                    <td class="{{ $a->accountability_signed }}">{{ ucfirst($a->accountability_signed) }}</td>
                    <td class="{{ $a->accountability_uploaded_snipeit }}">{{ ucfirst($a->accountability_uploaded_snipeit) }}</td>
                    <td>{{ optional($a->date_issued)->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="text-align: right; margin-top: 8px; color: #666; font-size: 9px;">
        Total assets: {{ $assets->count() }}
    </p>
</body>
</html>
