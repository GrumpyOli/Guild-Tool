@extends('layout')

@section('title', 'Guild Notes')

@section('mainContent')
<h1>Add a note</h1>
@if ( $errors->any() )
<div class="panel-error">
    @foreach ($errors->all() as $message)
    <p>{{ $message }}</p>
    @endforeach
</div>
@endif
<form action="{{ route('guild.notes_add') }}" method="post">
    @csrf
    <div class="forms-beauty">
        <table>
            <tr>
                <th>Message:</th>
                <td><textarea type="text" name="message" id="message">{{ old('message') }}</textarea>
                </td>
            </tr>
            <tr>
                <th>Link with a specifid character: (Optional)</th>
                <td><input type="text" name="character_id" id="character_id" value="{{ old('character_id') }}"></td>
            </tr>
            <tr>
                <th>&nbsp;</th>
                <td><input type="submit" value="Save"></td>
            </tr>
        </table>
    </div>
</form>

<h2>Character ID</h2>
<div class="two_columns">

    <div class="column">
        <table>
            <tr>
                <td colspan="2">Guild Members</td>
            </tr>
            @foreach( Guild()->members as $member )
            <tr>
                <td>{{ $member->name }}</td>
                <td>{{ $member->id }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="column">
        <table>
            <tr>
                <td colspan="2">Linked Members</td>
            </tr>
            @foreach( Guild()->linked_characters as $member )
            <tr>
                <td>{{ $member->name }}</td>
                <td>{{ $member->id }}</td>
            </tr>
            @endforeach
        </table>
    </div>

</div>

@endsection