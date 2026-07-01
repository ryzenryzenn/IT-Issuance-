<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetsExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private $query) {}

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'Company', 'Asset Tag', 'Asset Model', 'Serial Number', 'Category',
            'Assigned User', 'Location', 'RustDesk ID',
            'Accountability Signed', 'Uploaded to Snipe-IT',
            'Date Issued', 'Latest Updates / Remarks', 'Created At',
        ];
    }

    public function map($asset): array
    {
        return [
            $asset->company?->name,
            $asset->asset_tag,
            $asset->model?->name,
            $asset->serial_number,
            $asset->category?->name,
            $asset->assignee?->name,
            $asset->location?->name,
            $asset->rustdesk_id,
            $asset->accountability_signed,
            $asset->accountability_uploaded_snipeit,
            optional($asset->date_issued)->format('Y-m-d'),
            $asset->latest_updates_remarks,
            $asset->created_at?->format('Y-m-d H:i'),
        ];
    }
}
