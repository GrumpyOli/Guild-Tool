<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom API Request</title>
</head>
<body>
    <form action="{{ route('admin.api_request') }}" method="post">
        @csrf 
        <input type="text" name="url" id="url">
        <input type="submit" value="go">
    </form>
    @php
        if ( isset($DataObject) ){
            var_dump($DataObject);
        }
    @endphp
</body>
</html>