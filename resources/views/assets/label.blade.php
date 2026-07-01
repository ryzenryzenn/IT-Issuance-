<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asset Label — {{ $asset->asset_tag }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 24px;
            color: #111;
            display: flex;
            justify-content: center;
        }
        .label {
            width: 360px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 16px;
            display: flex;
            gap: 16px;
            align-items: center;
        }
        .qr { flex: 0 0 auto; }
        .qr svg { display: block; width: 130px; height: 130px; }
        .info { font-size: 13px; line-height: 1.5; }
        .info .row { margin-bottom: 6px; }
        .info .lbl { color: #6b7280; font-size: 11px; text-transform: uppercase; letter-spacing: .03em; }
        .info .val { font-weight: 600; word-break: break-word; }
        .toolbar { position: fixed; top: 12px; right: 12px; }
        .toolbar button {
            font-size: 13px; padding: 8px 14px; border: 0; border-radius: 6px;
            background: #4f46e5; color: #fff; cursor: pointer;
        }
        @media print {
            .toolbar { display: none; }
            body { padding: 0; }
            .label { border: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="toolbar">
        <button onclick="window.print()">Print</button>
    </div>

    <div class="label">
        <div class="qr">{!! $qrSvg !!}</div>
        <div class="info">
            <div class="row">
                <div class="lbl">Model</div>
                <div class="val">{{ $asset->model?->name }}</div>
            </div>
            <div class="row">
                <div class="lbl">Serial</div>
                <div class="val">{{ $asset->serial_number ?: 'N/A' }}</div>
            </div>
            <div class="row">
                <div class="lbl">Assignee</div>
                <div class="val">{{ $asset->assignee?->name ?: 'Unassigned' }}</div>
            </div>
        </div>
    </div>
</body>
</html>
