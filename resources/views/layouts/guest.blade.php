<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ClassPilot') }} — Ton espace d’études</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
        <meta name="theme-color" content="#244b3a">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen px-4 py-6 sm:px-8 sm:py-10">
            <header class="mx-auto flex max-w-6xl items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-xl focus-visible:outline">
                    <x-application-logo class="h-11 w-11 fill-current text-blue-700" />
                    <span class="text-xl font-bold tracking-tight text-slate-900">ClassPilot</span>
                </a>
                <a href="{{ route('home') }}" class="text-sm font-semibold text-slate-600 transition hover:text-slate-900">
                    Retour à l’accueil
                </a>
            </header>

            <main class="mx-auto grid max-w-6xl items-center gap-10 py-8 sm:py-14 lg:grid-cols-[minmax(0,1fr)_minmax(24rem,0.82fr)] lg:gap-16">
                <section class="hidden flex-col gap-6 lg:flex">
                    <span class="cp-eyebrow">Un espace clair pour apprendre</span>
                    <h1 class="cp-heading max-w-xl text-5xl xl:text-6xl">
                        Tes études, à ton rythme.
                    </h1>
                    <p class="max-w-lg text-lg leading-8 text-slate-600">
                        Retrouve tes tâches, tes devoirs et tes échéances dans un espace pensé pour t’aider à avancer, sans te disperser.
                    </p>
                    <div class="mt-3 flex max-w-lg flex-wrap gap-3">
                        <span class="rounded-full bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-800">Tâches organisées</span>
                        <span class="rounded-full bg-sand-100 px-4 py-2 text-sm font-semibold text-sand-800">Échéances visibles</span>
                        <span class="rounded-full bg-coral-50 px-4 py-2 text-sm font-semibold text-coral-800">Rappels utiles</span>
                    </div>
                    <div class="relative mt-5 overflow-hidden rounded-4xl border border-white/80 bg-gradient-to-br from-emerald-100 via-white to-sand-100 p-7 shadow-card">
                        <div class="absolute -right-7 -top-8 h-32 w-32 rounded-full bg-coral-100/80"></div>
                        <div class="relative flex items-center gap-4">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-2xl text-emerald-700 shadow-sm" aria-hidden="true">✦</span>
                            <p class="max-w-sm text-sm leading-6 text-slate-700">
                                Un peu d’organisation aujourd’hui, plus de sérénité pour la suite.
                            </p>
                        </div>
                    </div>
                </section>

                <section class="mx-auto w-full max-w-md rounded-4xl border border-white/90 bg-white/90 p-6 shadow-lift backdrop-blur sm:p-9">
                    {{ $slot }}
                </section>
            </main>

            <footer class="mx-auto max-w-6xl border-t border-slate-200/80 pt-5 text-center text-xs text-slate-500 sm:text-left">
                ClassPilot · Ton organisation scolaire, en un seul endroit.
            </footer>
        </div>
    </body>
</html>
