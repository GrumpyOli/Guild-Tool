<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guild-Tool</title>
</head>
<body>
    You must be logged to blizzard to continue. Click <a href="{{ $BlizzardLink }}">here</a> to log in with blizzard.

    @foreach ($errors->all() as $error)

  <div>{{ $error }}</div>

    @endforeach
</body>
</html>