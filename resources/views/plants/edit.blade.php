@extends('layouts.app')

@section('title', 'Edit plant | Plant Nursery Inventory')

@section('content')
    <section class="card">
        <h1>Edit {{ $plant->name }}</h1>
        <form method="POST" action="{{ route('plants.update', $plant) }}">
            @method('PUT')
            @include('plants._form', ['submitLabel' => 'Update plant'])
        </form>
    </section>
@endsection
