@extends('layout')

@section('title', 'Guild Roster')

@section('mainContent')
    <h1>Guild Roster</h1>
    
    <x-infos :messages="$messages"/>
    <p>There is currently {{ count( $Guild->members ) }} members</p>

    <table class="table-beauty" id="table_members">
        <tr>

            <th onclick="sortTable(0,'table_members')">Character</th>
            <th onclick="sortTable(1,'table_members')">Race</th>
            <th onclick="sortTable(2,'table_members')">Class</th>
            <th onclick="sortTable(3,'table_members')">Rank</th>
            <th onclick="sortTable(4,'table_members')">Level</th>
            <th onclick="sortTable(5,'table_members')">Tracked</th>
            
        </tr>

        @foreach( $Guild->members->sortBy('name')->sortBy('rank') as $Member )
        <tr>
            <td>
                {{ $Member->name }}
            </td>

            <td>
                {{ $Member->race->name }}
            </td>

            <td>
                {{ $Member->class->name }}
            </td>

            <td>
                {{ $Ranks[$Member->rank]->name ?? Null }}
            </td>

            <td>
                {{ $Member->level }}
            </td>

            <td class="center">
                <span id='td{{$Member->id}}'>{{ $trackedCharactersID->contains( $Member->id ) ? 'Yes' : 'No' }}</span>
                <a href="javascript:toggle_tracking({{ $Guild->id }},{{ $Member->id }}, 'td{{$Member->id}}')">(Change)</a>
            </td>
        </tr>
        @endforeach

    </table>
    
@endsection