<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Public Sans', 'Helvetica', Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 40px 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 4px solid #000000;
            box-shadow: 10px 10px 0px #000000;
            padding: 40px;
        }
        .header {
            text-align: center;
            border-bottom: 4px solid #000000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-weight: 900;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: -1px;
        }
        .content {
            font-size: 16px;
            line-height: 1.6;
            color: #000000;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #000000;
            font-size: 12px;
            text-align: center;
            opacity: 0.7;
        }
        .btn {
            display: inline-block;
            background-color: #A5B4FC; /* lila */
            color: #000000;
            text-decoration: none;
            padding: 15px 25px;
            font-weight: 900;
            border: 3px solid #000000;
            box-shadow: 5px 5px 0px #000000;
            margin: 20px 0;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>@yield('header', 'Lic. Nazarena De Luca')</h1>
        </div>
        <div class="content">
            @yield('content')
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Lic. Nazarena De Luca. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>
