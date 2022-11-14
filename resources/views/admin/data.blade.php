@extends('layout')

@section('title', 'Data')

@section('mainContent')
    <h1>Data</h1>
    
    @if ( session('status') )
    <p>{{ session('status') }}</p>
    @endif

    <h2>Quick command</h2>
    <nav>
        <a href="{{ route('admin.fetch_realm_data') }}">Fetch realm data</a>
        <a href="{{ route('admin.api_request') }}">Made a custom request</a>
    </nav>
    <h2>Token & API settings</h2>
    <table>
        <tr>
            <th>Region:</th>
            <td>{{ $Token_Region ?? 'Null' }}</td>
        </tr>
        <tr>
            <th>Scope:</th>
            <td>{{ $Token_Scope ?? 'Null' }}</td>
        </tr>
        <tr>
            <th>Access Token:</th>
            <td>{{ $Token_Number ?? 'Null' }}</td>
        </tr>
        <tr>
            <th>Expires:</th>
            <td>{{ $Token_Expires ?? 'Null' }}</td>
        </tr>
        <tr>
            <th>Language:</th>
            <td>{{ $Langugage ?? 'Null' }}</td>
        </tr>        
    </table>

    <h2>Guild in current session</h2>
    <table>
        <tr>
            <th>ID:</th>
            <td>{{ $Guild->id ?? 'Null' }}</td>
        </tr>        
        <tr>
            <th>Name:</th>
            <td>{{ $Guild->name ?? 'Null' }}</td>
        </tr>
        <tr>
            <th>Faction:</th>
            <td>{{ $Guild->faction ?? 'Null' }}</td>
        </tr>
        <tr>
            <th>Realm Slug:</th>
            <td>{{ $Guild->realmSlug ?? 'Null' }}</td>
        </tr>
        <tr>
            <th>Region:</th>
            <td>{{ $Guild->region ?? 'Null' }}</td>
        </tr>        
    </table>

@endsection