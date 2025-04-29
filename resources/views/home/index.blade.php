<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Smart Drinks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }

        .hero-section {
            padding: 4rem 2rem;
            background: linear-gradient(to right, #dfe9f3, #ffffff);
            text-align: center;
            border-radius: 1rem;
            margin-bottom: 3rem;
        }

        .card {
            border-radius: 1rem;
        }

        .recommendation-section {
            max-width: 960px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .highlight {
            color: #0d6efd;
        }

        footer {
            margin-top: 4rem;
            padding: 1.5rem 0;
            background: #343a40;
            color: #ffffff;
            text-align: center;
        }

        .card-img-top {
    height: 200px;
    object-fit: contain;
    background-color: #fff;

}

    </style>
</head>
<body>


<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">Smart Drinks 🥤</a>
    </div>
</nav>

<section class="hero-section container mt-4">
    <h1 class="mb-3">Welcome to <span class="highlight">Smart Drinks</span>!</h1>
    <p class="lead">Get real-time drink recommendations based on the weather and sales insights.</p>
</section>

<div class="container recommendation-section">
    <div class="row g-4 justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm p-4">
                <h2 class="section-title">🌤️ Weather Update</h2>
                    <h4>🌦 Weather in {{ $weatherData['city'] }}</h4>
                    <p>Temperature: {{ $weatherData['temperature'] }}°C</p>
                    <p>Condition: {{ $weatherData['condition'] }}</p>
                    <p><strong>Today's Suggestion:</strong> {{ $weatherData['recommendation'] }}</p>
                    <ul>
                        <li>Cold Drink Price:🧊 ${{ $weatherData['coldDrinkPrice'] }}</li>
                        <li>Hot Drink Price:☕ ${{ $weatherData['hotDrinkPrice'] }}</li>
                    </ul>
            </div>
        </div>
<div class="col-md-6">
    <div class="card shadow-sm p-4 border-primary">
        <h2 class="section-title">🤖 AI Suggestion</h2>
        <h5>Recommended Product ID: 12345</h5>
        <a href="#" class="btn btn-primary mb-2">View Product</a>
        <br>
        <form action="{{ route('chat') }}" method="POST">
    @csrf
    <button type="submit" class="btn btn-outline-success">💬 Talk to AI</button>
</form>

    </div>
</div>

    </div>
</div>
<div class="container mt-5">
    <h2 class="text-center mb-4">🍹 Explore Our Drinks</h2>

    <div class="row mb-4">
        <h4 class="text-primary">Cold Drinks</h4>
        <div class="col-md-3 mb-4">
            <div class="card h-80 shadow-sm">
                <img src={{ asset('assets/icedlemonade.jpeg') }} class="card-img-top" alt="Cold Drink 1">
                <div class="card-body">
                    <h5 class="card-title">Iced Lemonade</h5>
                    <a href="#" class="btn btn-outline-primary btn-sm">View</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
                <img src={{ asset('assets/mangosmoothie.jpeg') }} class="card-img-top" alt="Cold Drink 2">
                <div class="card-body">
                    <h5 class="card-title">Mango Smoothie</h5>
                    <a href="#" class="btn btn-outline-primary btn-sm">View</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
                <img src={{ asset('assets/icecoffee.jpeg') }} class="card-img-top" alt="Cold Drink 3">
                <div class="card-body">
                    <h5 class="card-title">Iced Coffee</h5>
                    <a href="#" class="btn btn-outline-primary btn-sm">View</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
                <img src={{asset('assets/icedtea.jpeg')}} class="card-img-top" alt="Cold Drink 4">
                <div class="card-body">
                    <h5 class="card-title">Iced Tea</h5>
                    <a href="#" class="btn btn-outline-primary btn-sm">View</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <h4 class="text-danger">Hot Drinks</h4>
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
                <img src={{ asset('assets/hotchocolate.jpeg') }} class="card-img-top" alt="Hot Drink 1">
                <div class="card-body">
                    <h5 class="card-title">Hot Chocolate</h5>
                    <a href="#" class="btn btn-outline-danger btn-sm">View</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
                <img src={{ asset('assets/cappaccino.jpeg') }} class="card-img-top" alt="Hot Drink 2">
                <div class="card-body">
                    <h5 class="card-title">Cappuccino</h5>
                    <a href="#" class="btn btn-outline-danger btn-sm">View</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
                <img src={{ asset('assets/latte.jpeg') }} class="card-img-top" alt="Hot Drink 3">
                <div class="card-body">
                    <h5 class="card-title">Latte</h5>
                    <a href="#" class="btn btn-outline-danger btn-sm">View</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
                <img src={{ asset('assets/americano.jpeg') }} class="card-img-top" alt="Hot Drink 4">
                <div class="card-body">
                    <h5 class="card-title">Americano</h5>
                    <a href="#" class="btn btn-outline-danger btn-sm">View</a>
                </div>
            </div>
        </div>
    </div>
</div>

<footer>
    <p>&copy; {{ date('Y') }} Smart Drinks. All rights reserved.</p>
</footer>

</body>
</html>
