@extends('layout')

@section('title', 'Data')

@section('mainContent')
    <h1>Data</h1>
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
@endsection