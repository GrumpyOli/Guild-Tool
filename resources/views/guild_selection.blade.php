<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <title>Guild-Tool - Guild Selection</title>
    <link rel="stylesheet" href="{{ asset('css/layout-guildSelection.css') }}">
</head>
<body>

<div class="container">
    <h1>Select a guild</h1>
    @if ( $errors->any() )
        <div class="error_message">{{ $errors->first() }}</div>
    @endif
    <p>First, you must choose a region and a realm name, then write down the guild you are looking for:</p>
    <form action="{{ route('GuildSelection') }}" method="post">
        @csrf
        <div class="boxes">
            <div class="box">

                <select name="region" id="region" class=".fitContent">
                    @foreach ( $Regions as $Region )
                    <option value="{{ $Region }}">{{ $Region }}</option>
                    @endforeach
                </select>

                <select name="realmSlug" id="realmSlug">
                @foreach ( $Realms as $Realm )
                    <option value="{{ $Realm->slug }}">{{ $Realm->name }}</option>
                    @endforeach
                </select>

                <input type="text" name="name" id="name">
            </div>
            <div class="box">
                <p>Or chosse from this list</p>
            </div>
        </div>
    </form>
</div>
    
</body>
</html>