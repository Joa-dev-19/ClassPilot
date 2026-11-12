<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="ClassPilot rassemble tes tâches scolaires, devoirs, échéances et rappels dans un espace simple à utiliser.">
        <meta name="theme-color" content="#244b3a">
        <title>{{ config('app.name', 'ClassPilot') }} — Tes études, bien organisées</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen font-sans text-slate-900 antialiased">
        <header class="relative z-10 border-b border-slate-200/80 bg-white/80 backdrop-blur">
            <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3.5 sm:px-8" aria-label="Navigation principale">
                <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-xl focus-visible:ring-2 focus-visible:ring-emerald-600">
                    <x-application-logo class="h-10 w-10 fill-current text-blue-700" />
                    <span class="text-lg font-bold tracking-tight text-slate-900">ClassPilot</span>
                </a>
                <div class="flex items-center gap-2 sm:gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="cp-button-primary px-4 py-2.5 text-sm">
                            Mon espace
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="whitespace-nowrap rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 sm:px-4">
                            Se connecter
                        </a>
                        <a href="{{ route('register') }}" class="cp-button-primary px-4 py-2.5 text-sm sm:px-5">
                            <span class="hidden sm:inline">Créer mon compte</span>
                            <span class="sm:hidden">S’inscrire</span>
                        </a>
                    @endauth
                </div>
            </nav>
        </header>

        <main>
            <section class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-12 sm:px-8 sm:py-20 lg:grid-cols-[1.04fr_0.96fr] lg:gap-16 lg:py-24">
                <div class="flex flex-col items-start">
                    <span class="cp-eyebrow inline-flex items-center gap-2 rounded-full bg-emerald-50 px-4 py-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-600" aria-hidden="true"></span>
                        Une organisation qui te ressemble
                    </span>
                    <h1 class="cp-heading mt-6 max-w-2xl text-4xl sm:text-5xl lg:text-6xl">
                        Une tête plus légère. Des études mieux organisées.
                    </h1>
                    <p class="mt-6 max-w-xl text-base leading-7 text-slate-600 sm:text-lg sm:leading-8">
                        Tes tâches, les devoirs de tes enseignants et les dates importantes, réunis au même endroit. Tu sais quoi faire et quand le faire.
                    </p>
                    <div class="mt-8 flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                        <a href="{{ route('register') }}" class="cp-button-primary w-full px-6 py-3 sm:w-auto">
                            Créer mon compte étudiant
                            <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4" aria-hidden="true">
                                <path d="M4.5 10h11m0 0-4.5-4.5M15.5 10 11 14.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                        <a href="{{ route('login') }}" class="cp-button-secondary w-full px-6 py-3 sm:w-auto">
                            J’ai déjà un compte
                        </a>
                    </div>
                    <p class="mt-4 text-xs leading-5 text-slate-500">
                        Inscription réservée aux étudiants. Les espaces enseignants et administrateurs sont sécurisés.
                    </p>
                </div>

                <div class="relative mx-auto w-full max-w-xl overflow-x-clip" aria-label="Aperçu de l’espace ClassPilot">
                    <div class="absolute -left-7 top-12 h-32 w-32 rounded-full bg-sand-200/70 blur-2xl" aria-hidden="true"></div>
                    <div class="absolute -right-5 bottom-8 h-40 w-40 rounded-full bg-coral-100/80 blur-2xl" aria-hidden="true"></div>
                    <div class="relative rotate-1 rounded-[2rem] border border-white/90 bg-gradient-to-br from-emerald-100 via-white to-sand-100 p-3 shadow-lift sm:p-5">
                        <div class="overflow-hidden rounded-[1.45rem] border border-slate-200/80 bg-white shadow-sm">
                            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <x-application-logo class="h-8 w-8 fill-current text-blue-700" />
                                    <div>
                                        <p class="text-xs font-bold text-slate-900">Mon espace</p>
                                        <p class="text-[11px] text-slate-500">Tout avance, étape par étape</p>
                                    </div>
                                </div>
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-semibold text-emerald-800">À jour</span>
                            </div>
                            <div class="flex flex-col gap-4 p-5 sm:p-6">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-medium text-slate-500">Ton organisation</p>
                                        <p class="mt-1 text-lg font-bold tracking-tight text-slate-900">Tout au même endroit.</p>
                                    </div>
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-sand-100 text-sand-800" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6">
                                            <path d="M7 3v3m10-3v3M4.5 9h15M6 5h12a1.5 1.5 0 0 1 1.5 1.5v12A1.5 1.5 0 0 1 18 20H6a1.5 1.5 0 0 1-1.5-1.5v-12A1.5 1.5 0 0 1 6 5Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                            <path d="m9 14 2 2 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="flex flex-col gap-3">
                                    <div class="flex items-center gap-3 rounded-2xl bg-emerald-50/80 p-3.5">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-emerald-700 shadow-sm" aria-hidden="true">✓</span>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-semibold text-slate-800">Tes tâches et révisions</p>
                                            <p class="mt-0.5 text-xs text-slate-500">Avance à ton rythme</p>
                                        </div>
                                        <span class="h-2 w-2 rounded-full bg-emerald-500" aria-hidden="true"></span>
                                    </div>
                                    <div class="flex items-center gap-3 rounded-2xl bg-sand-50 p-3.5">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-sand-700 shadow-sm" aria-hidden="true">
                                            <svg viewBox="0 0 20 20" fill="none" class="h-5 w-5">
                                                <path d="M4 3.5h8l4 4v9H4v-13Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                                <path d="M12 3.5v4h4M7 11h6M7 14h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                            </svg>
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-semibold text-slate-800">Tes devoirs de classe</p>
                                            <p class="mt-0.5 text-xs text-slate-500">Les bonnes infos, au bon moment</p>
                                        </div>
                                        <span class="h-2 w-2 rounded-full bg-amber-400" aria-hidden="true"></span>
                                    </div>
                                    <div class="flex items-center gap-3 rounded-2xl bg-coral-50/80 p-3.5">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-coral-700 shadow-sm" aria-hidden="true">
                                            <svg viewBox="0 0 20 20" fill="none" class="h-5 w-5">
                                                <path d="M10 3.5a5 5 0 0 0-5 5v2.1c0 .55-.2 1.08-.57 1.49L3.5 13h13l-.93-.91A2.1 2.1 0 0 1 15 10.6V8.5a5 5 0 0 0-5-5ZM8 16a2.1 2.1 0 0 0 4 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-semibold text-slate-800">Tes rappels</p>
                                            <p class="mt-0.5 text-xs text-slate-500">Une échéance ne passe plus inaperçue</p>
                                        </div>
                                        <span class="h-2 w-2 rounded-full bg-coral-500" aria-hidden="true"></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 border-t border-slate-100 pt-4 text-xs text-slate-500">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 font-bold text-emerald-800" aria-hidden="true">✓</span>
                                    Simple à consulter, même depuis ton téléphone.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="border-y border-slate-200/70 bg-white/55">
                <div class="mx-auto grid max-w-7xl gap-5 px-4 py-10 sm:grid-cols-3 sm:px-8 sm:py-12">
                    <article class="flex gap-4 rounded-2xl p-4">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5">
                                <path d="M5 6.5h14M5 12h14M5 17.5h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                <circle cx="3.5" cy="6.5" r=".7" fill="currentColor" /><circle cx="3.5" cy="12" r=".7" fill="currentColor" /><circle cx="3.5" cy="17.5" r=".7" fill="currentColor" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="font-semibold text-slate-900">Garde le fil</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-600">Tes travaux et priorités restent faciles à retrouver.</p>
                        </div>
                    </article>
                    <article class="flex gap-4 rounded-2xl p-4">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-sand-100 text-sand-800" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5">
                                <path d="M7 3v3m10-3v3M4.5 9h15M6 5h12a1.5 1.5 0 0 1 1.5 1.5v12A1.5 1.5 0 0 1 18 20H6a1.5 1.5 0 0 1-1.5-1.5v-12A1.5 1.5 0 0 1 6 5Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="font-semibold text-slate-900">Vois ce qui arrive</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-600">Les dates importantes sont réunies dans ton calendrier.</p>
                        </div>
                    </article>
                    <article class="flex gap-4 rounded-2xl p-4">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-coral-100 text-coral-800" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5">
                                <path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM10 21h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="font-semibold text-slate-900">Reçois un rappel</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-600">Choisis quand recevoir un rappel avant une échéance.</p>
                        </div>
                    </article>
                </div>
            </section>
        </main>

        <footer class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-7 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-8">
            <span class="font-semibold text-slate-700">ClassPilot</span>
            <span>Un peu mieux organisé, chaque jour.</span>
        </footer>
    </body>
</html>
