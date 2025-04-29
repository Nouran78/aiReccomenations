@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">All Orders</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped" id="orders-table">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Product ID</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order) <!-- Iterate over each order -->
            <tr id="order-{{ $order->id }}">
                <td>{{ $order->id }}</td> <!-- Access individual order properties -->
                <td>{{ $order->product_id }}</td>
                <td>{{ $order->quantity }}</td>
                <td>${{ number_format($order->price, 2) }}</td>
                <td>{{ $order->created_at }}</td>

                <td>
                    <a href="{{ route('orders.edit', ['id' => $order->id]) }}" class="btn btn-primary btn-sm">Edit</a>

                    <form action="{{ route('orders.destroy', ['id' => $order->id]) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this order?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Delete</button>
                    </form>

                    @if ($order->deleted_at)
                    <form action="{{ route('orders.restore', ['id' => $order->id]) }}" method="POST" style="display:inline-block;">
                        @csrf
                        <button class="btn btn-info btn-sm">Restore</button>
                    </form>

                    <form action="{{ route('orders.forceDelete', ['id' => $order->id]) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to permanently delete this order?');">
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
        const eventSource = new EventSource('/api/orders/stream');

        eventSource.onmessage = function(event) {
            const newOrder = JSON.parse(event.data);
            console.log('New order received:', newOrder);

            // Create a new row element for the new order
            const tableBody = document.querySelector('#orders-table tbody');
            const newRow = document.createElement('tr');
            newRow.id = `order-${newOrder.order_id}`;

            newRow.innerHTML = `
                <td>${newOrder.order_id}</td>
                <td>${newOrder.product_id}</td>
                <td>${newOrder.quantity}</td>
                <td>$${newOrder.price.toFixed(2)}</td>
                <td>${newOrder.created_at}</td>
                <td>
                    <a href="/orders/${newOrder.order_id}/edit" class="btn btn-primary btn-sm">Edit</a>
                    <form action="/orders/${newOrder.order_id}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this order?');">
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
