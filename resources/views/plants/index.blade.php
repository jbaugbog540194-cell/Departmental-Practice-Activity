@extends('layouts.app')

@section('title', 'Plants | Plant Nursery Inventory')

@section('content')
    <div class="actions" style="justify-content: space-between;">
        <div>
            <h1>Plant inventory</h1>
            <p class="muted">Manage the nursery's plant records.</p>
        </div>
        <a class="button" href="{{ route('plants.create') }}">Add plant</a>
    </div>

    @if ($plants->isEmpty())
        <section class="card">No plants have been added yet.</section>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                <tr><th>Plant</th><th>Category</th><th>Unit price</th><th>Stock</th><th>Created by</th><th>Actions</th></tr>
                </thead>
                <tbody>
                @foreach ($plants as $plant)
                    <tr>
                        <td>{{ $plant->name }}</td>
                        <td>{{ $plant->category }}</td>
                        <td>{{ number_format((float) $plant->unit_price, 2) }}</td>
                        <td>{{ $plant->stock_status === 'in_stock' ? 'In stock' : 'Out of stock' }}</td>
                        <td>{{ $plant->user->name }}</td>
                        <td class="actions">
                            <a href="{{ route('plants.show', $plant) }}">View</a>
                            <a href="{{ route('plants.edit', $plant) }}">Edit</a>
                            <form method="POST" action="{{ route('plants.destroy', $plant) }}" onsubmit="return confirm('Delete this plant?')">
                                @csrf
                                @method('DELETE')
                                <button class="danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $plants->links() }}</div>
    @endif
@endsection
