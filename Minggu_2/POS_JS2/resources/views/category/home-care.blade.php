<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home Care</title>
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
        .card-container{
            margin: 1em;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 2em;
        }
        .card{
            width: 200px;
            height: 200px;
            padding: 1em;
            background-color: #ccc;
            border: 1px black solid;
        }
    </style>
    <nav>
        <a href="/" style="font-size: 1.5em">POS</a>
    </nav>
    <main>
        <h1>Home Care</h1>
        <div class="card-container">
            <div class="card">Lorem, ipsum dolor.</div>
            <div class="card">Lorem, ipsum dolor.</div>
            <div class="card">Lorem, ipsum dolor.</div>
        </div>
    </main>
</body>
</html>