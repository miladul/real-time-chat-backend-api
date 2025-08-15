<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realtime Chat API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
            background: linear-gradient(135deg, #1f1f1f 0%, #0d0d0d 100%);
        }
        .hero-title {
            font-size: 3rem;
            font-weight: 700;
        }
        .hero-subtitle {
            font-size: 1.2rem;
            color: #aaa;
        }
        .btn-custom {
            background: #0d6efd;
            border: none;
            padding: 0.8rem 1.5rem;
            font-size: 1.1rem;
            border-radius: 50px;
            transition: 0.3s;
        }
        .btn-custom:hover {
            background: #0b5ed7;
            transform: translateY(-2px);
        }
        footer {
            text-align: center;
            padding: 1.5rem 0;
            background: #0d0d0d;
            color: #777;
            font-size: 0.9rem;
            border-top: 1px solid #222;
        }
    </style>
</head>
<body>

<section class="hero-section">
    <div>
        <h1 class="hero-title">Realtime Chat API</h1>
        <p class="hero-subtitle mt-3">
            Modern, fast, and secure API built with Laravel 12.<br>
            Perfect for real-time messaging applications.
        </p>
        <div class="mt-4">
            <a href="#" class="btn btn-custom">View API Docs</a>
            <a href="#" class="btn btn-outline-light ms-2">Get Started</a>
        </div>
    </div>
</section>

<footer>
    &copy; {{ date('Y') }} Realtime Chat API. Built with ❤️ using Laravel.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
