@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Edit Order #{{ $order->id }}</h1>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

@if (session('success') || isset($success))
<div class="alert alert-success">{{ session('success') ?? $success }}</div>
@endif

    <form action="{{ route('orders.update', $order->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label for="product_id">Product ID</label>
            <input type="text" name="product_id" id="product_id" class="form-control" value="{{ $order->product_id }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="quantity">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" value="{{  $order->quantity }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="price">Price ($)</label>
            <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ $order->price }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Order</button>

        <a href="{{ route('orders.dashboard') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
