<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin login — NOKSHI</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="min-h-screen bg-plum-950 flex items-center justify-center p-6">
    <div class="absolute inset-0 grain opacity-20 pointer-events-none"></div>
    <div class="relative w-full max-w-md">
        <div class="mb-8 text-center text-paper">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-marigold-400 font-display text-2xl text-plum-950">ন</span>
            <h1 class="mt-4 font-display text-3xl tracking-wide">NOKSHI</h1>
            <p class="mt-1 text-sm text-paper/50">Admin console</p>
        </div>
        <form method="POST" action="{{ route('admin.login') }}" class="rounded-3xl bg-white p-8 shadow-lift space-y-5">
            @csrf
            @if($errors->any())
                <div class="rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $errors->first() }}</div>
            @endif
            <div>
                <label class="adm-label">Email</label>
                <input type="email" name="email" value="{{ old('email', 'admin@nokshi.test') }}" class="adm-input" required autofocus>
            </div>
            <div>
                <label class="adm-label">Password</label>
                <input type="password" name="password" class="adm-input" required>
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="remember" value="1" class="adm-checkbox"> Remember me
            </label>
            <button class="btn btn-primary btn-lg w-full">Sign in</button>
            <p class="text-center text-xs text-ink-300">admin@nokshi.test / password</p>
        </form>
        <p class="mt-6 text-center text-sm text-paper/40"><a href="{{ route('home') }}" class="hover:text-paper">← Back to storefront</a></p>
    </div>
</body>
</html>
