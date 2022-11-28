@extends('layout')

@section('title', 'Guild Roster')

@section('mainContent')
<h1>Guild Roster</h1>

<h2>Add a character</h2>

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<p>You can add a character to the guild if this character is not in the guild by typing his name and server. Then click add</p>
<form action="{{ route('guild.linked_characters') }}" method="post">
    @csrf
    <input type="text" name="characterName" id="characterName">
    <select name="realmSlug" id="realmSlug">
        @foreach ( $realms as $realm )
        <option value="{{ $realm->slug }}">{{ $realm->name }}</option>
        @endforeach
    </select>
    <input type="submit" value="Add">
</form>

<h2>Current linked characters</h2>
<table class="table-beauty">
    <tr>

        <th>Character</th>
        <th>Race</th>
        <th>Class</th>
        <th>Level</th>
        <th>Tracked</th>
        <th>Link</th>

    </tr>

    @foreach( $Guild->linked_characters->sortBy('name')->sortBy('name') as $Character )
    <tr>
        <td>
            {{ $Character->name }}
        </td>

        <td>
            {{ $Character->race->name }}
        </td>

        <td>
            {{ $Character->class->name }}
        </td>

        <td>
            {{ $Character->level }}
        </td>

        <td class="center">
                <span id='td{{$Character->id}}'>{{ $trackedCharactersID->contains( $Character->id ) ? 'Yes' : 'No' }}</span>
                <a href="javascript:toggle_tracking({{ $Guild->id }},{{ $Character->id }}, 'td{{$Character->id}}')">(Change)</a>
        </td>

        <td>
            <a href="javascript:remove_link({{ $Guild->id }},{{ $Character->id }}, 'td{{$Character->id}}')">remove</a>
        </td>

    </tr>
    @endforeach

</table>

@endsection