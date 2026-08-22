<!DOCTYPE html>
<html lang="id" class="{{ $darkMode ?? '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PLMS Finance')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Nunito+Sans:wght@400;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/@tabler/icons@latest/iconfont/tabler-icons.min.css">
</head>
<body class="bg-[var(--color-bg)] font-[var(--font-body)] text-[var(--color-body)] antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <main class="flex-1 p-6 lg:p-8">
            {{ $slot }}
        </main>
    </div>
    @stack('scripts')
</body>
</html>
