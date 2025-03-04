<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $name }} Profile</title>
</head>
<body>
    <style>
        body{
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }
        nav{
            width:  auto;
            padding: 2em 3em;
            background-color: darkorange;
            color: #fff;
            display: flex;
            justify-content: space-between;
        }
        main{
            margin: 2em
        }
        a{
            color: #000;
            font-weight: bold;
            text-decoration: none;
        }
        a:hover{
            color: #fff;
        }
    </style>
    <nav>
        <a href="/" style="font-size: 1.5em">POS</a>
    </nav>
    <main>
        <h1>User ID: {{ $id }}</h1>
        <h1>User Name: {{ $name }}</h1>
    </main>
</body>
</html>