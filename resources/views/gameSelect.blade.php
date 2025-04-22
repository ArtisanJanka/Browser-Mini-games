<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- Tailwind via Laravel Vite --}}
    <style>
        #display-text span {
            white-space: pre-wrap;
        }
    </style>
</head>
<body class="bg-[#1e1e2f] text-[#f8f8f2] font-mono p-8 leading-relaxed">
    <header class="flex justify-center gap-6 mb-6 text-lg font-semibold">
    </header>
</body>
</html>