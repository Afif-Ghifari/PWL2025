<!DOCTYPE html> 
<html> 
    <head> 
        <title>Stock List</title> 
    </head> 
    <body> 
        <h1>Stock</h1> 

        @if(session('success')) 
            <p>{{ session('success') }}</p> 
        @endif 
        <a href="{{ route('stocks.create') }}">Add Stock</a> 
        <ul> 
            @foreach ($stocks as $stock) 
            <li> 
                {{ $stock->name }}, stok: {{ $stock->qty }}  -
                <a href="{{ route('stocks.edit', $stock) }}">Edit</a> 
                <form action="{{ route('stocks.destroy', $stock) }}" method="POST" style="display:inline;"> 
                    @csrf 
                    @method('DELETE') 
                    <button type="submit">Delete</button> 
                </form> 
            </li> 
            @endforeach 
        </ul> 
    </body> 
</html>