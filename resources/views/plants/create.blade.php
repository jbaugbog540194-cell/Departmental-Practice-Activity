@extends('layouts.app')

@section('title', 'Add plant | Plant Nursery Inventory')

@section('content')
    <section class="card">
        <h1>Add plant</h1>
        <form method="POST" action="{{ route('plants.store') }}">
            @include('plants._form', ['submitLabel' => 'Save plant'])
        </form>
    </section>
@endsection
