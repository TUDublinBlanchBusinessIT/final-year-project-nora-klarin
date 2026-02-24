<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CareHub</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">CareHub</a>

        @auth
            <div class="collapse navbar-collapse show">
                <ul class="navbar-nav me-auto">

                    @if(auth()->user()->role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.users.index') }}">Users</a>
                        </li>
                    @endif

                    @if(auth()->user()->role === 'social_worker')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('socialworker.dashboard') }}">Dashboard</a>
                        </li>
                    @endif

                    @if(auth()->user()->role === 'carer')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('carer.dashboard') }}">Dashboard</a>
                        </li>
                    @endif
                </ul>

                <span class="navbar-text me-3">
                    {{ auth()->user()->name }} ({{ ucfirst(str_replace('_',' ', auth()->user()->role)) }})
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        Logout
                    </button>
                </form>
            </div>
        @endauth
    </div>
</nav>
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn btn-link">Logout</button>
</form>


<main class="container">
    @yield('content')
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
