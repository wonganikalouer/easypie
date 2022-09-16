<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyPie Error</title>
    <style>
        body{
            margin: 0px;
            padding: 20px;
            font-family: monospace;
        }
        .easypie-header{
            background-color: orange;
            padding: 10px;
        }
    </style>
</head>
<body>
    <h1 class="easypie-header">EASYPie PHP Framework 1.0.1 <br>
        <small style="font-size:16px;">powered by gslib</small>
    </h1>
    <h1>Error: {{error-code}}</h1>
    <hr>
    <h3>{{error-message}}</h3>
    <i>To disable error messages from being displayed on the UI, please define("EASYPIE-DEBUG",false)</i> <br>
    <i>-OR- You can just redirect to another error screen</i>
</body>
</html>