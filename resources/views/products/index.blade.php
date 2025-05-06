@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">All Products</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bproducted table-striped" id="products-table">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Base Price</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product) <!-- Iterate over each product -->
            <tr id="product-{{ $product->id }}">
                <td>{{ $product->id }}</td> <!-- Access individual product properties -->
                <td>{{ $product->name }}</td>
                <td>{{ $product->category }}</td>
                <td>${{ number_format($product->base_price, 2) }}</td>
                <td>{{ $product->created_at }}</td>

                <td>
                    <a href="{{ route('products.edit', ['id' => $product->id]) }}" class="btn btn-primary btn-sm">Edit</a>

                    <form action="{{ route('products.destroy', ['id' => $product->id]) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger btn-sm">Delete</button>
                    </form>

                    @if ($product->deleted_at)
                    <form action="{{ route('products.restore', ['id' => $product->id]) }}" method="POST" style="display:inline-block;">
                        @csrf
                        <button class="btn btn-info btn-sm">Restore</button>

                    </form>

                    <form action="{{ route('products.forceDelete', ['id' => $product->id]) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to permanently delete this product?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Force Delete</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const eventSource = new EventSource('/api/products/stream');

        eventSource.onmessage = function(event) {
            const newproduct = JSON.parse(event.data);
            console.log('New product received:', newproduct);

            // Create a new row element for the new product
            const tableBody = document.querySelector('#products-table tbody');
            const newRow = document.createElement('tr');
            newRow.id = `product-${newproduct.product_id}`;

            newRow.innerHTML = `
                <td>${newproduct.id}</td>
                <td>${newproduct.name}</td>
                <td>${newproduct.category}</td>
                <td>$${newproduct.base_price.toFixed(2)}</td>
                <td>${newproduct.created_at}</td>
                <td>
                    <a href="/products/${newproduct.id}/edit" class="btn btn-primary btn-sm">Edit</a>
                    <form action="/products/${newproduct.id}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            `;

            // Append the new row to the table
            tableBody.appendChild(newRow);
        };
    });
</script>
@endsection
