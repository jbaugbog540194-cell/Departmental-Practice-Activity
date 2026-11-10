<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Services\PlantReportPdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PlantReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->validatedFilters($request);
        $plants = $this->filteredPlants($filters)->paginate(15)->withQueryString();
        $categories = Plant::query()->distinct()->orderBy('category')->pluck('category');

        return view('reports.index', compact('plants', 'categories', 'filters'));
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $plants = $this->filteredPlants($this->validatedFilters($request))->get();

        return response()->streamDownload(function () use ($plants): void {
            $output = fopen('php://output', 'w');
            if ($output === false) {
                throw new \RuntimeException('Unable to open the report output stream.');
            }

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Plant name', 'Category', 'Unit price', 'Description', 'Stock status', 'Created by']);

            foreach ($plants as $plant) {
                fputcsv($output, [
                    $this->safeSpreadsheetCell($plant->name),
                    $this->safeSpreadsheetCell($plant->category),
                    $plant->unit_price,
                    $this->safeSpreadsheetCell($plant->description),
                    $plant->stock_status === 'in_stock' ? 'In stock' : 'Out of stock',
                    $this->safeSpreadsheetCell($plant->user->name),
                ]);
            }

            fclose($output);
        }, 'plant-report.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPdf(Request $request): Response
    {
        $plants = $this->filteredPlants($this->validatedFilters($request))->get();
        $pdf = app(PlantReportPdf::class)->generate($plants);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="plant-report.pdf"',
        ]);
    }

    private function validatedFilters(Request $request): array
    {
        return $request->validate([
            'category' => ['nullable', 'string', 'max:100'],
            'stock_status' => ['nullable', Rule::in(['in_stock', 'out_of_stock'])],
            'report_type' => ['nullable', Rule::in(['all', 'in_stock', 'out_of_stock', 'by_category', 'price_list'])],
        ]);
    }

    private function filteredPlants(array $filters): Builder
    {
        return Plant::query()
            ->with('user')
            ->when($filters['category'] ?? null, fn (Builder $query, string $category) => $query->where('category', $category))
            ->when($filters['stock_status'] ?? null, fn (Builder $query, string $status) => $query->where('stock_status', $status))
            ->when(
                ($filters['report_type'] ?? 'all') === 'in_stock',
                fn (Builder $query) => $query->where('stock_status', 'in_stock')
            )
            ->when(
                ($filters['report_type'] ?? 'all') === 'out_of_stock',
                fn (Builder $query) => $query->where('stock_status', 'out_of_stock')
            )
            ->orderBy(
                ($filters['report_type'] ?? null) === 'by_category' ? 'category' : 'name'
            );
    }

    private function safeSpreadsheetCell(string $value): string
    {
        return preg_match('/^[\s]*[=+\-@]/u', $value) === 1 ? "'".$value : $value;
    }
}
