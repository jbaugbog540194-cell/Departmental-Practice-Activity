@extends('layouts.app')

@section('title', 'Plant reports | Plant Nursery Inventory')

@section('content')
    <h1>Plant reports</h1>
    <p class="muted">Filter the current inventory, then export the same result as Excel-compatible CSV or PDF.</p>
    <section class="card">
        <form method="GET" action="{{ route('reports.index') }}">
            <label for="report_type">Report</label>
            <select id="report_type" name="report_type">
                <option value="all" @selected(($filters['report_type'] ?? 'all') === 'all')>All plants</option>
                <option value="in_stock" @selected(($filters['report_type'] ?? '') === 'in_stock')>In-stock plants</option>
                <option value="out_of_stock" @selected(($filters['report_type'] ?? '') === 'out_of_stock')>Out-of-stock plants</option>
                <option value="by_category" @selected(($filters['report_type'] ?? '') === 'by_category')>Plants by category</option>
                <option value="price_list" @selected(($filters['report_type'] ?? '') === 'price_list')>Plant price list</option>
            </select>

            <label for="category">Category</label>
            <select id="category" name="category">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
                @endforeach
            </select>

            <label for="stock_status">Stock status</label>
            <select id="stock_status" name="stock_status">
                <option value="">All statuses</option>
                <option value="in_stock" @selected(($filters['stock_status'] ?? '') === 'in_stock')>In stock</option>
                <option value="out_of_stock" @selected(($filters['stock_status'] ?? '') === 'out_of_stock')>Out of stock</option>
            </select>
            <p class="actions">
                <button type="submit">Apply filters</button>
                <a class="button secondary" href="{{ route('reports.index') }}">Clear</a>
            </p>
        </form>
        <div class="actions">
            <a class="button" href="{{ route('reports.export.excel', request()->query()) }}">Export Excel (CSV)</a>
            <a class="button" href="{{ route('reports.export.pdf', request()->query()) }}">Export PDF</a>
        </div>
    </section>

    <section class="card">
        <h2>{{ $plants->total() }} matching plants</h2>
        @if ($plants->isEmpty())
            <p>No plants match these filters.</p>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr><th>Plant</th><th>Category</th><th>Unit price</th><th>Description</th><th>Stock status</th></tr>
                    </thead>
                    <tbody>
                    @foreach ($plants as $plant)
                        <tr>
                            <td>{{ $plant->name }}</td>
                            <td>{{ $plant->category }}</td>
                            <td>{{ number_format((float) $plant->unit_price, 2) }}</td>
                            <td>{{ $plant->description }}</td>
                            <td>{{ $plant->stock_status === 'in_stock' ? 'In stock' : 'Out of stock' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination">{{ $plants->links() }}</div>
        @endif
    </section>
@endsection
