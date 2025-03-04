<!DOCTYPE html>
<html>
<head>
    <title>Add Stock</title>
</head>
<body>
    <h1>Add Stock</h1>
    <form action="{{ route('stocks.store') }}" method="POST">
        @csrf
        <label for="name">Name:</label>
        <input type="text" name="name" required>
        <br>
        <label for="qty">Quantity:</label>
        <input type="number" name="qty" required>
        <br>
        <button type="submit">Add Stock</button>
    </form>
    <a href="{{ route('stocks.index') }}">Back to List</a>
</body>
</html>
