@extends('layout')

@section('title', 'Guild Roster')

@section('mainContent')
<h1>Account</h1>
<div class="button-update">
    @if ( session('message') )
    {{ session('message') }}
    @else
    Last update was: {{ Auth()->User()->account->updated_at }}
    @endif
    <a href="{{ route('account.update') }}">Update</a>
</div>
<h2>Basic information</h2>
<table>
    <tr>
        <td>Battle tag: </td>
        <td>{{ auth()->user()->account->battle_tag }}</td>
    </tr>
</table>
<h2>Character related with this account</h2>
<div class="panel-informations">Here is the list of every character linked to your battle.net account. Don't worry if you don't see any data about the guild associated to your character. It happens if we did not look for this specific guild before. <br><br>You can click on headers to sort.</div>
<table class="table-beauty" id="table_characters">
    <tr>
        <th onclick="sortTable(0,'table_characters')">Character</th>
        <th onclick="sortTable(1,'table_characters')">Race</th>
        <th onclick="sortTable(2,'table_characters')">Class</th>
        <th onclick="sortTable(3,'table_characters')">Faction</th>
        <th onclick="sortTable(4,'table_characters')">Realm</th>
        <th onclick="sortTable(5,'table_characters')">Guild</th>
    </tr>
    @foreach ( Auth()->User()->account->characters->sortBy('name') as $Character )
    <tr>
        <td>{{ $Character->name }}</td>
        <td>{{ $Character->race->name }}</td>
        <td>{{ $Character->class->name }}</td>
        <td>{{ $Character->faction }}</td>
        <td>{{ $Character->realm->name }}</td>
        <td>{{ $Character->guild->name ?? Null }}</td>
    </tr>
    @endforeach
</table>
@endsection