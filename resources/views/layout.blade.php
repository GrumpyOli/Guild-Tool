<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guild-Tool - @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('public/css/layout-main.css') .'?'. rand() }}">
    <link rel="stylesheet" href="{{ asset('public/css/tables.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
    <script src="{{ asset('resources/js/functions.js') }}"></script>
    <script src="{{ asset('resources/js/tables.js') }}"></script>
</head>
<body>

    <header class="layout-header">Header</header>
    @section('Menu')
    <nav class="layout-menu">
        <a href="#">Dashboard</a>
        <a href="#">Groups</a>
        <a href="#">Tracking</a>
        <a href="#">Weekly Vault</a>
        <a href="{{ route('guild.roster') }}">Roster</a>
        <a href="{{ route('guild.linked_characters') }}">Linked characters</a>
        <a href="{{ route('guild.notes') }}">Notes</a>
        <a href="{{ route('account.summary') }}">Account</a>
        <a href="{{ route('guild.settings') }}">Settings</a>
        <a href="{{ route('Logout') }}">Logout</a>
        <a href="{{ route('admin.data') }}">Data (For dev)</a>
    </nav>
    @show

    <div class="layout-maintContent">
    @yield('mainContent', 'Default Value')
    </div>
</body>
</html>