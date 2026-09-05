<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ISHAP — Skrining Mandiri ISPA & Kesehatan Pernapasan')</title>
    <meta name="description" content="@yield('meta_description', 'Aplikasi skrining mandiri risiko ISPA berbasis web modern dengan deteksi gejala, edukasi patogenesis, dan rekomendasi faskes terdekat.')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS CDN for rich responsive styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        },
                        medteal: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                        },
                        slateDark: '#0f172a'
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .hero-pattern {
            background-image: radial-gradient(#059669 0.75px, transparent 0.75px), radial-gradient(#0d9488 0.75px, #f8fafc 0.75px);
            background-size: 30px 30px;
            background-position: 0 0, 15px 15px;
            opacity: 0.25;
        }

        .badge-pulse {
            animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse-ring {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: .7;
                transform: scale(1.05);
            }
        }
    </style>
    @stack('styles')
</head>

<body class="min-h-screen flex flex-col antialiased">
    <!-- Navbar Minimalis Medis -->
    <header
        class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200 transition-all duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <!-- Logo & Brand -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <!-- Logo Rumah Sakit Standar (Palang Medis Minimalis) -->
                    <div
                        class="w-9 h-9 rounded-lg bg-[#0F5144] flex items-center justify-center text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-bold tracking-tight text-slate-900">ISHAP</span>
                            <span
                                class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 text-[#0F5144] border border-slate-200">Kesehatan</span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium">Intelligent Screening for Health Awareness & Prevention</p>
                    </div>
                </a>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('home') }}"
                        class="px-3.5 py-2 text-sm font-semibold rounded-lg transition {{ request()->routeIs('home') ? 'text-[#0F5144] bg-emerald-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        Beranda
                    </a>
                    <a href="{{ route('screening.index') }}"
                        class="px-3.5 py-2 text-sm font-semibold rounded-lg transition {{ request()->routeIs('screening.*') ? 'text-[#0F5144] bg-emerald-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        Skrining Gejala
                    </a>
                    <a href="{{ route('facilities.index') }}"
                        class="px-3.5 py-2 text-sm font-semibold rounded-lg transition {{ request()->routeIs('facilities.*') ? 'text-[#0F5144] bg-emerald-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        Faskes Terdekat
                    </a>
                    <a href="{{ route('doctors.index') }}"
                        class="px-3.5 py-2 text-sm font-semibold rounded-lg transition {{ request()->routeIs('doctors.*') ? 'text-[#0F5144] bg-emerald-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        Dokter Online
                    </a>
                </nav>

                <!-- Action Button -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('screening.index') }}"
                        class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold text-white bg-[#0F5144] hover:bg-[#0B3C32] transition-colors border border-[#0B3C32]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Mulai Skrining
                    </a>
                </div>

            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 mt-24 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <!-- Col 1: About -->
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                        class="w-9 h-9 rounded-lg bg-[#0F5144] flex items-center justify-center text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                        <span class="text-xl font-bold text-white">ISHAP</span>
                    </div>
                    <p class="text-sm leading-relaxed text-slate-400 max-w-md mb-4">
                        Inisiatif Skrining Mandiri Infeksi Saluran Pernapasan Akut (ISPA). Membantu masyarakat mengenali
                        dini gejala pernapasan, memahami etiologi penyakit, dan menemukan fasilitas kesehatan rujukan
                        secara cepat.
                    </p>
                </div>

                <!-- Col 2: Navigasi Cepat -->
                <div>
                    <h4 class="text-sm font-semibold uppercase tracking-wider text-slate-200 mb-4">Fitur Utama</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-emerald-400 transition">Beranda &
                                Edukasi</a></li>
                        <li><a href="{{ route('screening.index') }}" class="hover:text-emerald-400 transition">Skrining
                                Gejala Mandiri</a></li>
                        <li><a href="{{ route('facilities.index') }}"
                                class="hover:text-emerald-400 transition">Fasilitas Kesehatan Terdekat</a></li>
                        <li><a href="{{ route('doctors.index') }}" class="hover:text-emerald-400 transition">Telemedika
                                Dokter Mitra</a></li>
                    </ul>
                </div>

                <!-- Col 3: Darurat -->
                <div>
                    <h4 class="text-sm font-semibold uppercase tracking-wider text-slate-200 mb-4">Kontak Darurat Medis
                    </h4>
                    <p class="text-xs text-slate-400 mb-3">Layanan gawat darurat 24 jam Indonesia:</p>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-800 text-xs">
                            <span class="text-slate-300">Ambulans / Kemenkes</span>
                            <span class="font-mono font-bold text-emerald-400">119</span>
                        </div>
                        <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-800 text-xs">
                            <span class="text-slate-300">Darurat Nasional</span>
                            <span class="font-mono font-bold text-emerald-400">112</span>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="mt-12 pt-8 border-t border-slate-800 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p>&copy; {{ date('Y') }} ISHAP. Dibangun dengan Laravel MVC & Standar Skrining Respirasi.</p>
                <p>Terminologi Medis: Skrining Mandiri & Perkiraan Risiko.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>
