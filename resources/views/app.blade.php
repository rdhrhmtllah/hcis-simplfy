<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('components.header')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @vite('resources/js/app.js')
    @vite('resources/js/shell.js')
    @inertiaHead
</head>
<body>
    @include('components.topbar', [
        'user' => Auth::user(),
        'notifications' => $notifications ?? [],
        'messages' => $messages ?? [],
        'unreadCount' => $unreadCount ?? 0,
        'activeModule' => $activeModule ?? 'APP',
        'activeModuleUrl' => $activeModuleUrl ?? '/homepage'
    ])

    <div id="app1">
        @include('components.sidebar')
        <div id="main">
            @inertia
        </div>
    </div>
</body>
</html>
