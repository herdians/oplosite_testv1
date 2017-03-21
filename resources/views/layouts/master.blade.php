<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>@yield('title')</title>
    </head>
    <body>
        <header>
            <nav>
                <a href="/">Home</a>
            </nav>
        </header>
            <br>
                <div class="container">
                    @yield('content')
                </div>
            <br>
        <footer>
            <br>
            <p>
                &copy; Laravel & Herdian 2017
            </p>
            <br>
        </footer>

    </body>
</html>
