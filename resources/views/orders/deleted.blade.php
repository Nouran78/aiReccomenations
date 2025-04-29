@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Deleted Orders</h1>

    <a href="{{ route('orders.index') }}" class="btn btn-success mb-3">Back to Orders</a>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Product ID</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Deleted At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->product_id }}</td>
                <td>{{ $order->quantity }}</td>
                <td>${{ number_format($order->price, 2) }}</td>
                <td>{{ $order->deleted_at }}</td>
                <td>
                <form action="{{ url('orders/' . $order->id . '/restore') }}" method="POST" style="display:inline-block;">
    @csrf
    <button class="btn btn-info btn-sm">Restore</button>
</form>

<form action="{{ url('orders/' . $order->id . '/force-delete') }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Permanently delete this order?');">
    @csrf
    @method('DELETE')
    <button class="btn btn-danger btn-sm">Force Delete</button>
</form>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
