<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Plant Nursery Inventory')</title>
    <style>
        :root { color-scheme: light; font-family: Arial, sans-serif; color: #23332a; background: #f4f7f3; }
        body { margin: 0; }
        header { background: #245c3b; color: white; padding: 1rem max(1rem, calc((100% - 1100px) / 2)); display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
        header a { color: white; text-decoration: none; margin-right: 1rem; }
        header form { display: inline; }
        main { max-width: 1100px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; border: 1px solid #dfe7df; border-radius: 8px; padding: 1.25rem; margin-bottom: 1rem; }
        .actions { display: flex; align-items: center; gap: .75rem; flex-wrap: wrap; }
        .button, button { display: inline-block; border: 0; border-radius: 5px; background: #245c3b; color: white; padding: .65rem 1rem; text-decoration: none; cursor: pointer; font: inherit; }
        .button.secondary { background: #53665a; }
        .button.danger, button.danger { background: #a33636; }
        label { display: block; font-weight: 600; margin: .85rem 0 .3rem; }
        input, select, textarea { box-sizing: border-box; width: 100%; max-width: 560px; border: 1px solid #aebcaf; border-radius: 4px; padding: .6rem; font: inherit; }
        input[type=checkbox] { width: auto; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: .7rem; border-bottom: 1px solid #dfe7df; text-align: left; vertical-align: top; }
        th { background: #eaf1e9; }
        .table-wrap { overflow-x: auto; }
        .status { padding: .75rem; border-radius: 5px; background: #e4f3e7; margin-bottom: 1rem; }
        .error { color: #a33636; }
        .muted { color: #5b6b60; }
        .pagination { margin-top: 1rem; }
    </style>
</head>
<body>
<header>
    <div>
        <a href="{{ route('plants.index') }}"><strong>Plant Nursery Inventory</strong></a>
        @auth
            <a href="{{ route('plants.index') }}">Plants</a>
            <a href="{{ route('reports.index') }}">Reports</a>
        @endauth
    </div>
    @auth
        <div>
            <span>{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="secondary" type="submit">Log out</button>
            </form>
        </div>
    @endauth
</header>
<main>
    @if (session('status'))
        <div class="status" role="status">{{ session('status') }}</div>
    @endif
    @yield('content')
</main>
</body>
</html>
