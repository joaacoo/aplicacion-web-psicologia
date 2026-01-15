<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Lic. Nazarena De Luca</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/jpeg" href="{{ asset('img/069b6f01-e0b6-4089-9e31-e383edf4ff62.jpg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&family=Syne:wght@700;800&family=Manrope:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --color-rosa: #ff90e8;
            --color-amarillo: #ffc900;
            --color-celeste: #a3e1ff;
            --color-verde: #23a094;
            --color-lila: #b8c1ec;
            --color-dark: #000000;
        }

        body {
            background-color: var(--color-celeste);
            font-family: 'Outfit', sans-serif;
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .error-container {
            background: white;
            border: 4px solid #000;
            box-shadow: 12px 12px 0px #000;
            padding: 3rem;
            max-width: 500px;
            margin: 1rem;
            border-radius: 20px;
        }

        .error-code {
            font-family: 'Syne', sans-serif;
            font-size: 8rem;
            font-weight: 900;
            line-height: 1;
            margin: 0;
            -webkit-text-stroke: 3px #000;
            color: var(--color-amarillo);
        }

        .error-title {
            font-family: 'Syne', sans-serif;
            font-size: 2rem;
            margin: 1.5rem 0 1rem;
            line-height: 1.1;
        }

        .error-message {
            font-family: 'Manrope', sans-serif;
            color: #555;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .neobrutalist-btn {
            display: inline-block;
            text-decoration: none;
            background: var(--color-rosa);
            color: #000;
            padding: 1rem 2rem;
            font-weight: 800;
            font-size: 1.1rem;
            border: 3px solid #000;
            box-shadow: 4px 4px 0px #000;
            transition: all 0.2s;
            cursor: pointer;
            border-radius: 10px;
        }

        .neobrutalist-btn:hover {
            transform: translate(-3px, -3px);
            box-shadow: 7px 7px 0px #000;
        }

        .neobrutalist-btn:active {
            transform: translate(2px, 2px);
            box-shadow: 2px 2px 0px #000;
        }
    </style>
</head>
<body>
    <div class="error-container">
        @yield('content')
        <div style="margin-top: 2rem;">
            <a href="{{ url('/') }}" class="neobrutalist-btn">
                <i class="fa-solid fa-house"></i> Volver al Inicio
            </a>
        </div>
    </div>
</body>
</html>
