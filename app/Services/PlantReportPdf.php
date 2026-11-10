<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;

class PlantReportPdf
{
    public function generate(Collection $plants): string
    {
        $lines = [
            'Plant Nursery Inventory Report',
            'Generated: '.now()->format('Y-m-d H:i'),
            str_repeat('-', 92),
            'PLANT | CATEGORY | UNIT PRICE | STOCK STATUS | DESCRIPTION',
            str_repeat('-', 92),
        ];

        foreach ($plants as $plant) {
            $description = mb_substr(preg_replace('/\s+/', ' ', $plant->description) ?? '', 0, 38);
            $lines[] = sprintf(
                '%s | %s | %s | %s | %s',
                mb_substr($plant->name, 0, 22),
                mb_substr($plant->category, 0, 14),
                $plant->unit_price,
                $plant->stock_status === 'in_stock' ? 'In stock' : 'Out of stock',
                $description
            );
        }

        if ($plants->isEmpty()) {
            $lines[] = 'No plants match the selected filters.';
        }

        $pages = array_chunk($lines, 50);
        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            2 => '',
            3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Courier /Encoding /WinAnsiEncoding >>',
        ];
        $pageReferences = [];

        foreach ($pages as $index => $pageLines) {
            $pageId = 4 + ($index * 2);
            $contentId = $pageId + 1;
            $pageReferences[] = $pageId.' 0 R';

            $commands = "BT\n/F1 8 Tf\n40 752 Td\n11 TL\n";
            foreach ($pageLines as $line) {
                $commands .= '('.$this->escapeText($line).") Tj\nT*\n";
            }
            $commands .= 'ET';

            $objects[$pageId] = sprintf(
                '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 3 0 R >> >> /Contents %d 0 R >>',
                $contentId
            );
            $objects[$contentId] = '<< /Length '.strlen($commands)." >>\nstream\n".$commands."\nendstream";
        }

        $objects[2] = '<< /Type /Pages /Kids ['.implode(' ', $pageReferences).'] /Count '.count($pages).' >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $id => $object) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id." 0 obj\n".$object."\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n0000000000 65535 f \n";
        for ($id = 1; $id <= count($objects); $id++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$id]);
        }
        $pdf .= 'trailer << /Size '.(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n".$xrefOffset."\n%%EOF";

        return $pdf;
    }

    private function escapeText(string $text): string
    {
        $text = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text) ?: '';

        return str_replace(['\\', '(', ')', "\r", "\n"], ['\\\\', '\\(', '\\)', ' ', ' '], $text);
    }
}
