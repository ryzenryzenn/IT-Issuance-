@props(['assets', 'label'])

<div class="copy">
    <div class="copy-label">{{ $label }}</div>

    <div class="head">
        <div>
            <div class="corp">JOSEFINA REALTY CORPORATION</div>
            <div class="addr">
                8th Floor Victoria Building<br>
                429 United Nations Avenue,<br>
                Ermita, Manila
            </div>
        </div>
        <div style="text-align:right">
            <div class="control">CONTROL NO. <span class="fill">&nbsp;</span></div>
            <div class="gate">GATE&nbsp;PASS</div>
        </div>
    </div>

    <div class="meta">
        <div><span class="lbl">DATE OF MOVE IN / OUT :</span> <span class="gp-date fill">&nbsp;</span></div>
        <div style="margin-top:4px"><span class="lbl">TENANT / COMPANY :</span> <span class="fill">&nbsp;</span></div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>QUANTITY</th>
                <th>DESCRIPTION OF ITEMS</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($assets as $a)
                <tr>
                    <td class="qty">1</td>
                    <td class="desc">{{ $a->asset_tag }} - {{ $a->model?->name ?: 'N/A' }} ({{ $a->category?->name ?: 'N/A' }}) - {{ $a->assignee?->name ?: 'N/A' }} going to {{ $a->location?->name ?: 'N/A' }}</td>
                </tr>
            @endforeach
            {{-- pad to keep the form's shape --}}
            @for ($i = $assets->count(); $i < 5; $i++)
                <tr><td class="qty">&nbsp;</td><td class="desc">&nbsp;</td></tr>
            @endfor
        </tbody>
    </table>

    <table class="signs">
        <tr>
            <td>
                <div class="lead">Authorized By :</div>
                <span class="signoff-line">&nbsp;</span>
                <div class="cap">TENANTS / COMPANY</div>
            </td>
            <td></td>
        </tr>
        <tr>
            <td style="padding-top:18px">
                <div class="lead">Checked By:</div>
                <span class="signoff-line">&nbsp;</span>
                <div class="cap">(Guard's Name &amp; Signature)</div>
            </td>
            <td style="padding-top:18px">
                <div class="lead">Approved By:</div>
                <span class="signoff-line">&nbsp;</span>
                <div class="cap">BUILDING ADMIN.</div>
            </td>
        </tr>
    </table>
</div>
