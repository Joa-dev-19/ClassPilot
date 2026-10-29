<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="ClassPilot aide les élèves et étudiants à organiser leurs tâches scolaires et leurs échéances.">
        <title>{{ config('app.name', 'ClassPilot') }} — Organise tes études</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
        <header class="border-b border-slate-200 bg-white">
            <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-4 sm:px-8" aria-label="Navigation principale">
                <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-700 text-lg font-bold text-white" aria-hidden="true">C</span>
                    <span class="text-xl font-bold">ClassPilot</span>
                </a>
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-blue-800 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-600">
                            Mon espace
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-600">
                            Se connecter
                        </a>
                        <a href="{{ route('register') }}" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">
                            Créer un compte
                        </a>
                    @endauth
                </div>
            </nav>
        </header>

        <main>
            <section class="mx-auto grid max-w-7xl items-center gap-10 px-5 py-16 sm:px-8 sm:py-24 lg:grid-cols-2">
                <div>
                    <p class="font-semibold uppercase tracking-wide text-blue-700">Tes études, mieux organisées</p>
                    <h1 class="mt-4 max-w-2xl text-4xl font-bold tracking-tight text-slate-950 sm:text-5xl">
                        Avance dans tes études, une tâche à la fois.
                    </h1>
                    <p class="mt-6 max-w-xl text-lg leading-8 text-slate-600">
                        Centralise tes devoirs, projets et révisions. Garde tes échéances en vue et organise ton travail depuis un seul espace.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('register') }}" class="rounded-xl bg-blue-700 px-6 py-3 font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">
                            Créer mon compte étudiant
                        </a>
                        <a href="{{ route('login') }}" class="rounded-xl border border-slate-300 bg-white px-6 py-3 font-semibold text-slate-800 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">
                            J’ai déjà un compte
                        </a>
                    </div>
                </div>

                <div class="rounded-3xl border border-blue-100 bg-white p-6 shadow-sm sm:p-8" aria-label="Fonctionnalités de ClassPilot">
                    <h2 class="text-xl font-bold">Tout ton travail au même endroit</h2>
                    <ul class="mt-6 space-y-5">
                        <li class="flex gap-4">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-800" aria-hidden="true">1</span>
                            <div>
                                <h3 class="font-semibold">Organise tes tâches</h3>
                                <p class="mt-1 text-sm leading-6 text-slate-600">Note tes devoirs, projets et révisions avec leur priorité et leur date limite.</p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-800" aria-hidden="true">2</span>
                            <div>
                                <h3 class="font-semibold">Visualise tes échéances</h3>
                                <p class="mt-1 text-sm leading-6 text-slate-600">Consulte ton calendrier et programme des rappels pour ne rien oublier.</p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-800" aria-hidden="true">3</span>
                            <div>
                                <h3 class="font-semibold">Suis ton avancement</h3>
                                <p class="mt-1 text-sm leading-6 text-slate-600">Retrouve tes tâches et leur état d’avancement dans ton espace personnel.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>
        </main>

        <footer class="border-t border-slate-200 bg-white">
            <div class="mx-auto max-w-7xl px-5 py-6 text-sm text-slate-600 sm:px-8">
                ClassPilot — Organise tes études, avance vers tes objectifs.
            </div>
        </footer>
    </body>
</html>
