@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Weather & Drink Recommendation</h2>

    <div class="card">
        <div class="card-body">
            <h4>Current Temperature: {{ $temperature }} °C</h4>
            <h4>City: {{ $city }}</h4>
            <h5>Condition: {{ $condition }}</h5>
            <hr>

            <h3 class="text-primary">Recommended for today:</h3>
            <p class="lead">{{ $recommendation }}</p>

            <hr>
            <h4>🍹 Cold Drink Price: ${{ $coldDrinkPrice }}</h4>
            <h4>☕ Hot Drink Price: ${{ $hotDrinkPrice }}</h4>
        </div>
    </div>
</div>
@endsection
