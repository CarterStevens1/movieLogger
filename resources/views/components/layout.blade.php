<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:ital,wght@0,100..600;1,100..600&display=swap"
        rel="stylesheet">
    <title>Board Tracking</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#191919] text-white pb-12">
    <div class="px-10">
        <nav class="flex justify-between items-center py-4 border-b border-white/10">
            <div>
                <a href="{{ url('/') }}">
                    <a href="{{ url('/') }}">Home</a>
                </a>
            </div>
            @auth
                <div class="space-x-6 font-bold">
                    <a href="{{ url('/my-boards') }}">Boards</a>
                </div>

                <div class="space-x-6 font-bold flex">
                    <a href="{{ url('/edit') }}">Edit</a>
                    <form method="POST" action="/logout">
                        @csrf
                        @method('POST')
                        <button class="cursor-pointer">Log Out</button>
                    </form>
                </div>

            @endauth

            @guest
                <div class="space-x-6 font-bold">
                    <a href="{{ url('/register') }}">Sign Up</a>
                    <a href="{{ url('/login') }}">Log In</a>
                </div>
            @endguest

        </nav>


        <main class="mt-10 max-w-[986px] mx-auto">
            {{ $slot }}
        </main>
    </div>
</body>

</html>
