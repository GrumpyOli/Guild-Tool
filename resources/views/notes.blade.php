@extends('layout')

@section('title', 'Guild Notes')

@section('mainContent')
<h1>Notes</h1>

<div class="button-add"><a href="{{ route('guild.notes_add') }}">Add</a></div>

@foreach( Guild()->notes as $note )
<div class="note">
    <div class="owner">{{ $note->account->battle_tag }}</div>
    <div class="message">{{ $note->message }}</div>
    <div class="timestamps">{{ $note->created_at }}</div>
</div>
@endforeach
@endsection