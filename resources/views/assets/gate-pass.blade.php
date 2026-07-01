<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gate Pass</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            margin: 0;
            padding: 22px 44px;
            font-size: 13px;
        }
        .copy { page-break-inside: avoid; }
        .copy-label {
            text-align: right; font-weight: 700; font-size: 11px; letter-spacing: 1px;
            color: #555; border: 1px solid #999; border-radius: 4px; padding: 1px 8px;
            display: inline-block; float: right;
        }
        .head { display: flex; justify-content: space-between; align-items: flex-start; clear: both; }
        .corp { font-weight: 700; font-size: 15px; }
        .addr { font-size: 10px; line-height: 1.4; margin-top: 2px; }
        .control { font-size: 12px; margin-top: 6px; }
        .control .fill { border-bottom: 1px solid #111; display: inline-block; min-width: 150px; }
        .gate { text-align: right; font-weight: 700; font-size: 22px; margin-top: 10px; }

        .meta { margin: 16px 0 6px; }
        .meta .lbl { font-weight: 700; }
        .meta .fill { border-bottom: 1px solid #111; display: inline-block; min-width: 300px; }

        table.items { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.items th, table.items td { border: 1px solid #111; padding: 4px 8px; vertical-align: top; }
        table.items th { font-weight: 700; text-align: center; letter-spacing: 1px; }
        table.items td.qty { width: 15%; text-align: center; }
        table.items td.desc { width: 85%; }
        table.items tbody tr { height: 24px; }

        .signs { width: 100%; margin-top: 22px; }
        .signs td { vertical-align: top; padding-right: 30px; width: 50%; }
        .signoff-line { border-bottom: 1px solid #111; height: 22px; min-width: 220px; display: block; margin-bottom: 4px; }
        .cap { font-weight: 700; font-size: 11px; }
        .lead { font-weight: 700; margin-bottom: 22px; }

        .cutline {
            text-align: center; color: #777; font-size: 11px; letter-spacing: 2px;
            border-top: 1px dashed #999; margin: 22px 0; padding-top: 4px;
        }

        .toolbar { position: fixed; top: 14px; right: 16px; }
        .toolbar button {
            font-size: 13px; padding: 8px 14px; border: 0; border-radius: 6px;
            background: #003049; color: #fff; cursor: pointer;
        }
        @media print {
            .toolbar { display: none; }
            body { padding: 14px 24px; }
        }
    </style>
</head>
<body>
    <div class="toolbar"><button onclick="doPrint()">Print</button></div>

    @include('assets._gate-pass-copy', ['assets' => $assets, 'label' => 'ORIGINAL COPY'])

    <div class="cutline">&#9986; - - - - - - - - - - - - - - - - - - - - - - cut here - - - - - - - - - - - - - - - - - - - - - -</div>

    @include('assets._gate-pass-copy', ['assets' => $assets, 'label' => 'DUPLICATE COPY'])

    <script>
        function fillDate() {
            var today = new Date().toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
            document.querySelectorAll('.gp-date').forEach(function (el) { el.textContent = today; });
        }
        function doPrint() {
            fillDate();
            window.print();
        }
        window.addEventListener('load', doPrint);
    </script>
</body>
</html>
