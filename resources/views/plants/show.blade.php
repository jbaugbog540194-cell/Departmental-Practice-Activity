@extends('layouts.app')

@section('title', $plant->name.' | Plant Nursery Inventory')

@section('content')
    <section class="card">
        <h1>{{ $plant->name }}</h1>
        <p><strong>Category:</strong> {{ $plant->category }}</p>
        <p><strong>Unit price:</strong> {{ number_format((float) $plant->unit_price, 2) }}</p>
        <p><strong>Stock status:</strong> {{ $plant->stock_status === 'in_stock' ? 'In stock' : 'Out of stock' }}</p>
        <p><strong>Description:</strong><br>{{ $plant->description }}</p>
        <p><strong>Created by:</strong> {{ $plant->user->name }}</p>
        <div class="actions">
            <a class="button" href="{{ route('plants.edit', $plant) }}">Edit plant</a>
            <a class="button secondary" href="{{ route('plants.index') }}">Back to inventory</a>
        </div>
    </section>
@endsection
