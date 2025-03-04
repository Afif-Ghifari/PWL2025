<!DOCTYPE html>
<html>
<head>
    <title>Edit Stock</title>
</head>
<body>
    <h1>Edit Stock</h1>
    <form action="{{ route('stocks.update', $stock) }}" method="POST">
        @csrf
        @method('PUT')
        <label for="name">Name:</label>
        <input type="text" name="name" value="{{ $stock->name }}" required>
        <br>
        <label for="qty">Quantity:</label>
        <input type="number"  name="qty" value="{{ $stock->qty }}" required>
        <br>
        <button type="submit">Update stock</button>
    </form>
    <a href="{{ route('stocks.index') }}">Back to List</a>
</body>
</html>
