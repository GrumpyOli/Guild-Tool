<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guild-Tool - @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/mainLayout.css') }}">
</head>
<body>

    <header class="layout-header">Header</header>
    
    @section('Menu')
    <nav class="layout-menu">
        <ul>
            <li>Dashboard</li>
            <li>Manage Groups</li>
            <li>Manage Tracking</li>
            <li>View weekly vault</li>
            <li>View guild roster</li>
            <li>Guild Notes</li>
            <li>Manage account</li>
            <li>Disconnet</li>
            <li><a href="{{ route('Data') }}">Data (For dev)</a></li>
        </ul>
    </nav>
    @show

    <div class="layout-maintContent">
    @yield('mainContent', 'Default Value')
    </div>
</body>
</html>