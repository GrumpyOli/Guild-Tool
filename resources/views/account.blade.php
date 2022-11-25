@extends('layout')

@section('title', 'Guild Roster')

@section('mainContent')
    <h1>Account</h1>
        <table>
            <tr>
                <td>Battle tag: </td>
                <td>{{ auth()->user()->account->battle_tag }}</td>
            </tr>
        </table>
@endsection