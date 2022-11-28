@extends('layout')

@section('title', 'Guild Roster')

@section('mainContent')
    <h1>Guild Roster</h1>
    
    <p>There is currently {{ count( $Guild->members ) }} members</p>

    <table class="table-beauty">
        <tr>

            <th>Character</th>
            <th>Race</th>
            <th>Class</th>
            <th>Rank</th>
            <th>Level</th>
            <th>Tracked</th>
            
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
                {{  $trackedCharactersID->contains( $Member->id ) ? 'Yes' : 'No' }} <a href="{{ route('guild.change_tracking', $Member->id)}}">(Change)</a>
            </td>

        </tr>
        @endforeach

    </table>
    
@endsection