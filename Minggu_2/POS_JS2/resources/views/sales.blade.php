<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Beauty & Health</title>
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
        table {
            width: 100%;
            border: 1px solid black;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 18px;
            text-align: left;
        }
        th, td {
            padding: 10px;
            border: 1px solid black;
        }
    </style>
    <nav>
        <a href="/" style="font-size: 1.5em">POS</a>
    </nav>
    <main>
        <h1>Sales</h1>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Product 1</td>
                    <td>10</td>
                    <td>1000</td>
                </tr>
            </tbody>
        </table>
    </main>
</body>
</html>