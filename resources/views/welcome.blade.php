<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portfolio | Roeun</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-[Inter] antialiased">

<div class="min-h-screen flex flex-col lg:flex-row">

    <!-- SIDEBAR -->
    <aside class="w-full lg:w-80 bg-gradient-to-b from-gray-900 to-gray-800 text-white 
                  lg:h-screen lg:sticky top-0 shadow-xl">

        <!-- Profile Section -->
        <div class="p-8 text-center border-b border-gray-700">
            <div class="w-28 h-28 mx-auto rounded-full overflow-hidden 
                        border-4 border-indigo-500 shadow-lg">
                <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400"
                     class="w-full h-full object-cover">
            </div>

            <h2 class="mt-4 text-2xl font-bold">Roeun Dary</h2>
            <p class="text-indigo-400 text-sm mt-1">Full Stack Developer</p>
        </div>

        <!-- Navigation -->
        <nav class="p-6 space-y-3">

            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl 
                               bg-indigo-600/30 hover:bg-indigo-600 transition-all duration-200">
                <span>🏠</span> Home
            </a>

            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl 
                               hover:bg-white/10 transition-all duration-200">
                <span>👨‍💻</span> About
            </a>

            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl 
                               hover:bg-white/10 transition-all duration-200">
                <span>📂</span> Projects
            </a>

            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl 
                               hover:bg-white/10 transition-all duration-200">
                <span>📞</span> Contact
            </a>

        </nav>

        <!-- Footer -->
        <div class="absolute bottom-0 w-full p-4 text-center text-gray-400 text-sm border-t border-gray-700">
            © {{ date('Y') }} Roeun
        </div>

    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 p-8 lg:p-12 bg-white">

        <div class="max-w-5xl mx-auto">

            <!-- Hero Section -->
            <section class="mb-16">
                <h1 class="text-4xl lg:text-5xl font-bold text-gray-800 mb-4">
                    Hello, I'm Roeun 👋
                </h1>

                <p class="text-lg text-gray-600 leading-relaxed">
                    I build modern web applications using Laravel, Tailwind CSS,
                    and modern frontend technologies. I focus on clean design,
                    performance, and user experience.
                </p>
            </section>

            <!-- Cards Section -->
            <section class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                <div class="bg-gray-50 p-6 rounded-2xl border hover:shadow-lg transition duration-300">
                    <h3 class="text-xl font-semibold mb-2">Recent Project</h3>
                    <p class="text-gray-600 text-sm">
                        POS & Inventory System built with Laravel 12 + API.
                    </p>
                </div>

                <div class="bg-gray-50 p-6 rounded-2xl border hover:shadow-lg transition duration-300">
                    <h3 class="text-xl font-semibold mb-2">Current Focus</h3>
                    <p class="text-gray-600 text-sm">
                        API Development, Vue 3, and performance optimization.
                    </p>
                </div>

                <div class="bg-gray-50 p-6 rounded-2xl border hover:shadow-lg transition duration-300">
                    <h3 class="text-xl font-semibold mb-2">Available For</h3>
                    <p class="text-gray-600 text-sm">
                        Freelance projects & Full-time opportunities.
                    </p>
                </div>

            </section>

        </div>

    </main>

</div>

</body>
</html>uuu