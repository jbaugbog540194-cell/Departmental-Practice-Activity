@extends('layouts.app')

@section('title', 'Sign in | Plant Nursery Inventory')

@section('content')
    <section class="card" style="max-width: 560px; margin: 3rem auto;">
        <h1>Administrator sign in</h1>
        <p class="muted">Use your email address or username and password.</p>
        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <label for="login">Email or username</label>
            <input id="login" name="login" value="{{ old('login') }}" required autofocus autocomplete="username">
            @error('login') <p class="error">{{ $message }}</p> @enderror

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password">
            @error('password') <p class="error">{{ $message }}</p> @enderror

            <p><label><input type="checkbox" name="remember" value="1"> Remember me</label></p>
            <button type="submit">Sign in</button>
        </form>
    </section>
@endsection
