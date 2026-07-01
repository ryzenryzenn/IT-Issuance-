<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transmittal Form</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            margin: 0;
            padding: 40px 60px;
            font-size: 15px;
        }
        h1 { text-align: center; font-size: 22px; letter-spacing: 1px; margin: 0 0 28px; }
        .line { display: inline-block; border-bottom: 2px solid #111; min-width: 280px; }
        .row { margin-bottom: 6px; }
        .row b { font-weight: 700; }

        table.items { width: 100%; border-collapse: collapse; margin: 18px 0 30px; }
        table.items th, table.items td {
            border: 2px solid #111; padding: 8px 10px; text-align: left; height: 26px;
            font-size: 13px; vertical-align: top;
        }
        table.items th { font-weight: 700; }

        .section { margin-top: 28px; }
        .section .label { font-weight: 700; }
        .fill { border-bottom: 2px solid #111; display: inline-block; min-width: 230px; }

        .signoff { width: 100%; margin-top: 40px; }
        .signoff td { width: 50%; vertical-align: top; padding-right: 30px; }
        .signoff .blk { font-weight: 700; margin-bottom: 8px; }
        .signoff .ln { font-weight: 700; margin-bottom: 10px; }
        .signoff .fill { min-width: 200px; }

        .toolbar { position: fixed; top: 14px; right: 16px; }
        .toolbar button {
            font-size: 13px; padding: 8px 14px; border: 0; border-radius: 6px;
            background: #003049; color: #fff; cursor: pointer;
        }
        @media print {
            .toolbar { display: none; }
            body { padding: 20px 30px; }
        }
    </style>
</head>
<body>
    <div class="toolbar"><button onclick="doPrint()">Print</button></div>

    <h1>TRANSMITTAL FORM</h1>

    <div class="row"><b>Date:</b> <span id="print-date" class="line">&nbsp;</span></div>

    <table class="items">
        <thead>
            <tr>
                <th style="width:22%">Item (Asset Tag)</th>
                <th style="width:18%">Category</th>
                <th style="width:25%">Model</th>
                <th>Assigned User &rarr; Location</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($assets as $a)
                <tr>
                    <td>{{ $a->asset_tag }}</td>
                    <td>{{ $a->category?->name ?: 'N/A' }}</td>
                    <td>{{ $a->model?->name ?: 'N/A' }}</td>
                    <td>{{ $a->assignee?->name ?: 'N/A' }} going to {{ $a->location?->name ?: 'N/A' }}</td>
                </tr>
            @endforeach
            {{-- pad to a minimum of 5 rows so the form keeps its shape --}}
            @for ($i = $assets->count(); $i < 5; $i++)
                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            @endfor
        </tbody>
    </table>

    <div class="section">
        <div class="label">To:</div>
        <br>
        <div class="row"><b>Name:</b> <span class="fill">&nbsp;</span></div>
        <div class="row"><b>Department:</b> <span class="fill">&nbsp;</span></div>
        <div class="row"><b>Contact Information:</b> <span class="fill">&nbsp;</span></div>
    </div>

    <table class="signoff">
        <tr>
            <td>
                <div class="blk">Delivered by:</div>
                <div class="ln">Name: <span class="fill">&nbsp;</span></div>
                <div class="ln">Signature: <span class="fill">&nbsp;</span></div>
                <div class="ln">Date: <span class="fill">&nbsp;</span></div>
            </td>
            <td>
                <div class="blk">Received By:</div>
                <div class="ln">Name: <span class="fill">&nbsp;</span></div>
                <div class="ln">Signature: <span class="fill">&nbsp;</span></div>
                <div class="ln">Date: <span class="fill">&nbsp;</span></div>
            </td>
        </tr>
    </table>

    <script>
        function fillDate() {
            var today = new Date().toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
            document.getElementById('print-date').textContent = today;
        }
        function doPrint() {
            fillDate();
            window.print();
        }
        // Fill the date and open the print dialog automatically on load.
        window.addEventListener('load', doPrint);
    </script>
</body>
</html>
