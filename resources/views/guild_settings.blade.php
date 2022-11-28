@extends('layout')

@section('title', 'Guild Roster')

@section('mainContent')
<h1>Guild Rank</h1>

<x-errorPanel/>

@if ( session('status') )
<p>{{ session('status') }}</p>
@endif

<table class="table-beauty">
    <form action="{{ route('guild.update_rank') }}" method="POST">
        @csrf
        <tr>
            <th>Rank number 0</th>
            <td><input type="text" name="rank0" value="{{ $Ranks[0]->name }}"></td>
        </tr>
        <tr>
            <th>Rank number 1</th>
            <td><input type="text" name="rank1" value="{{ $Ranks[1]->name }}"></td>
        </tr>
        <tr>
            <th>Rank number 2</th>
            <td><input type="text" name="rank2" value="{{ $Ranks[2]->name }}"></td>
        </tr>
        <tr>
            <th>Rank number 3</th>
            <td><input type="text" name="rank3" value="{{ $Ranks[3]->name }}"></td>
        </tr>
        <tr>
            <th>Rank number 4</th>
            <td><input type="text" name="rank4" value="{{ $Ranks[4]->name }}"></td>
        </tr>
        <tr>
            <th>Rank number 5</th>
            <td><input type="text" name="rank5" value="{{ $Ranks[5]->name }}"></td>
        </tr>
        <tr>
            <th>Rank number 6</th>
            <td><input type="text" name="rank6" value="{{ $Ranks[6]->name }}"></td>
        </tr>
        <tr>
            <th>Rank number 7</th>
            <td><input type="text" name="rank7" value="{{ $Ranks[7]->name }}"></td>
        </tr>
        <tr>
            <th>Rank number 8</th>
            <td><input type="text" name="rank8" value="{{ $Ranks[8]->name }}"></td>
        </tr>
        <tr>
            <th>Rank number 9</th>
            <td><input type="text" name="rank9" value="{{ $Ranks[9]->name }}"></td>
        </tr>
        <tr>
            <th>&nbsp;</th>
            <td><input type="submit" value="Save"></td>
        </tr>
    </form>
</table>

@endsection