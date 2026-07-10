<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MailCampaign — Broadcast Manager</title>
    
    <!-- Google Fonts: Space Grotesk for display, Inter for body, JetBrains Mono for code/labels -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        /* Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 */
        /* Hallmark · genre: modern-minimal · macrostructure: Workbench · theme: Cobalt · enrichment: none · nav: N13 · footer: Ft2 */
        
        :root {
            --color-paper: oklch(98.5% 0.004 250);
            --color-paper-2: oklch(96% 0.006 250);
            --color-rule: oklch(91% 0.008 250);
            --color-rule-2: oklch(82% 0.01 250);
            --color-neutral: oklch(56% 0.008 250);
            --color-muted: oklch(40% 0.008 250);
            --color-ink: oklch(24% 0.02 258);
            --color-ink-2: oklch(34% 0.018 257);
            --color-accent: oklch(58% 0.20 256); /* electric cobalt */
            --color-accent-ink: oklch(98.5% 0.004 250);
            --color-focus: oklch(70% 0.19 256);
            --color-graphite: oklch(22% 0.016 260); /* dark graphite for code card and dark band */
            --color-graphite-ink: oklch(94% 0.006 260);
            --color-graphite-rule: oklch(30% 0.008 260);

            --font-display: 'Space Grotesk', sans-serif;
            --font-body: 'Inter', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;

            --space-3xs: 0.25rem;
            --space-2xs: 0.5rem;
            --space-xs: 0.75rem;
            --space-sm: 1rem;
            --space-md: 1.5rem;
            --space-lg: 2rem;
            --space-xl: 3rem;
            --space-2xl: 4.5rem;
            --space-3xl: 7rem;

            --radius-card: 10px;
            --radius-input: 6px;
            --radius-pill: 9999px;
            
            --ease-out: cubic-bezier(0.16, 1, 0.3, 1);
        }

        body {
            background-color: var(--color-paper);
            color: var(--color-ink-2);
            font-family: var(--font-body);
            overflow-x: clip;
        }

        .font-display { font-family: var(--font-display); }
        .font-mono { font-family: var(--font-mono); }
        .font-body { font-family: var(--font-body); }

        /* Reveal observer animation */
        .reveal {
            opacity: 0;
            transform: translateY(10px);
            transition: opacity .6s var(--ease-out), transform .6s var(--ease-out);
        }
        .reveal.is-in {
            opacity: 1;
            transform: none;
        }
        @media (prefers-reduced-motion: reduce) {
            .reveal {
                opacity: 1;
                transform: none;
                transition: none;
            }
        }

        /* Cobalt style rules */
        .code-card {
            background-color: var(--color-graphite);
            border: 1px solid var(--color-graphite-rule);
            border-radius: var(--radius-card);
            box-shadow: 0 1px 2px rgba(24, 30, 41, 0.05);
            font-family: var(--font-mono);
        }
    </style>
</head>
<body class="h-full min-h-screen flex flex-col justify-between selection:bg-[var(--color-accent)] selection:text-[var(--color-accent-ink)]">

    <!-- Nav Archetype: N13 Inline ⌘K search pill + wordmark + CTA -->
    <nav class="sticky top-0 z-30 w-full border-b border-[var(--color-rule)] bg-[var(--color-paper)]/85 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <div class="w-7 h-7 rounded-[6px] border border-[var(--color-accent)] flex items-center justify-center text-[var(--color-accent)] font-display font-semibold transition-colors duration-200">
                        M
                    </div>
                    <span class="font-display font-semibold text-sm tracking-tight text-[var(--color-ink)]">MailCampaign</span>
                </a>
                
                <!-- ⌘K Search Pill -->
                <button onclick="openPalette()" class="hidden md:flex items-center gap-3 px-3 py-1.5 rounded-[6px] border border-[var(--color-rule)] bg-[var(--color-paper-2)] hover:bg-[var(--color-rule)] text-[var(--color-muted)] hover:text-[var(--color-ink-2)] text-xs transition-all font-mono cursor-pointer outline-none focus:ring-1 focus:ring-[var(--color-accent)]">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Tìm lệnh nhanh...</span>
                    <kbd class="px-1.5 py-0.5 rounded bg-[var(--color-paper)] border border-[var(--color-rule-2)] text-[10px]">⌘K</kbd>
                </button>
            </div>

            <div class="flex items-center gap-4">
                @auth
                    <div class="flex items-center gap-3">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-7 h-7 rounded-full border border-[var(--color-rule)] object-cover">
                        @else
                            <div class="w-7 h-7 rounded-full bg-[var(--color-paper-2)] border border-[var(--color-rule)] text-[var(--color-ink)] flex items-center justify-center text-xs font-semibold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <span class="hidden sm:inline text-xs font-medium text-[var(--color-ink)]">{{ $user->name }}</span>
                        
                        <form action="{{ route('logout') }}" method="POST" id="logout-form" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 text-xs border border-[var(--color-rule)] rounded-[6px] hover:border-red-500/30 hover:text-red-600 transition-colors bg-[var(--color-paper)] text-[var(--color-ink-2)] font-medium cursor-pointer">
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('google.login') }}" class="px-3 py-1.5 text-xs font-semibold bg-[var(--color-accent)] hover:bg-[var(--color-accent)]/90 text-[var(--color-accent-ink)] rounded-[6px] transition-colors shadow-sm cursor-pointer flex items-center gap-2">
                        Đăng nhập
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content wrapper -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-6 py-10">

        <!-- Flash Message Banners -->
        @if(session('success') || session('error') || session('info'))
            <div class="mb-8 animate-fade-in reveal is-in">
                @if(session('success'))
                    <div class="border border-green-500/20 bg-green-500/5 text-green-700 px-4 py-3 rounded-[6px] flex items-start gap-3 text-sm">
                        <span class="font-mono text-xs uppercase tracking-wider text-green-600 font-semibold mt-0.5">[ SUCCESS ]</span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="border border-red-500/20 bg-red-500/5 text-red-700 px-4 py-3 rounded-[6px] flex items-start gap-3 text-sm">
                        <span class="font-mono text-xs uppercase tracking-wider text-red-600 font-semibold mt-0.5">[ ERROR ]</span>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('info'))
                    <div class="border border-[var(--color-accent)]/20 bg-[var(--color-accent)]/5 text-[var(--color-ink)] px-4 py-3 rounded-[6px] flex items-start gap-3 text-sm">
                        <span class="font-mono text-xs uppercase tracking-wider text-[var(--color-accent)] font-semibold mt-0.5">[ INFO ]</span>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif
            </div>
        @endif

        @guest
            <!-- Guest State: Asymmetric Hero / Workbench style with API code sample -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 py-12 items-center">
                
                <!-- Left: Headline + Action -->
                <div class="lg:col-span-6 space-y-6 reveal">
                    <span class="font-mono text-[10px] uppercase tracking-[0.06em] text-[var(--color-accent)] font-semibold">
                        // BROADCAST API PLATFORM
                    </span>
                    <h2 class="font-display font-semibold text-4xl lg:text-5xl text-[var(--color-ink)] tracking-[-0.03em] leading-tight">
                        Gửi email quảng bá
                    </h2>
                    <p class="text-sm leading-relaxed text-[var(--color-ink-2)] max-w-lg">
                        Công cụ gửi email quảng bá linh hoạt dành cho các nhà phát triển. Kết nối tài khoản Google để thực hiện cấu hình, quản lý và phân phối thư hàng loạt nhanh chóng, an toàn.
                    </p>

                    <div class="pt-2">
                        <a href="{{ route('google.login') }}" class="inline-flex items-center justify-center gap-3 px-6 py-3.5 bg-[var(--color-accent)] hover:bg-[var(--color-accent)]/95 text-[var(--color-accent-ink)] rounded-[6px] font-semibold text-sm transition-all hover:-translate-y-0.5 cursor-pointer shadow-md focus:ring-2 focus:ring-offset-2 focus:ring-[var(--color-accent)]">
                            <!-- Google SVG Logo -->
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="currentColor"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="currentColor" opacity="0.8"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="currentColor" opacity="0.9"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="currentColor" opacity="0.85"/>
                            </svg>
                            <span>Kết nối Google & Bắt đầu</span>
                        </a>
                    </div>
                </div>

                <!-- Right: Gmail Logo Image -->
                <div class="lg:col-span-6 flex justify-center items-center reveal">
                    <div class="relative w-64 h-64 md:w-80 md:h-80 flex items-center justify-center">
                        <div class="absolute inset-0 rounded-full border border-[var(--color-rule-2)] animate-[spin_60s_linear_infinite]"></div>
                        <img src="{{ asset('gmail-logo.png') }}" alt="Gmail Logo" class="w-48 h-48 md:w-56 md:h-56 object-contain relative z-10 transition-transform duration-500 hover:scale-105 select-none drop-shadow-[0_4px_20px_rgba(0,0,0,0.02)]">
                    </div>
                </div>

            </div>
        @else
            <!-- Authenticated State: Workbench Layout (Signature 7) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left: Compose Form -->
                <div class="lg:col-span-5 border border-[var(--color-rule)] bg-[var(--color-paper-2)] p-6 rounded-[10px] space-y-6 reveal">
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs uppercase tracking-wider text-[var(--color-accent)] font-semibold">[ 01 ]</span>
                        <h3 class="font-display font-semibold text-lg text-[var(--color-ink)]">Soạn chiến dịch mới</h3>
                    </div>

                    <form action="{{ route('email.send') }}" method="POST" id="campaign-form" class="space-y-5">
                        @csrf
                        
                        <!-- Dynamic Recipients -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="block font-mono text-[10px] uppercase tracking-[0.06em] text-[var(--color-muted)] font-semibold">
                                    Danh sách người nhận
                                </label>
                                <button type="button" onclick="addRecipient()" class="text-[10px] font-mono text-[var(--color-accent)] hover:underline cursor-pointer border-none bg-transparent outline-none">
                                    + Thêm email
                                </button>
                            </div>
                            
                            <div id="recipients-container" class="space-y-2">
                                <div class="flex items-center gap-2 recipient-row">
                                    <input type="checkbox" checked onchange="toggleRecipientInput(this)" class="w-3.5 h-3.5 accent-[var(--color-accent)] cursor-pointer">
                                    <input type="email" name="recipients[]" value="devmelon2601@gmail.com" required placeholder="name@example.com" class="flex-1 px-2.5 py-1.5 text-xs rounded-[6px] border border-[var(--color-rule-2)] bg-[var(--color-paper)] text-[var(--color-ink)] placeholder-[var(--color-muted)] outline-none focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] transition-all">
                                    <button type="button" onclick="removeRecipientRow(this)" class="text-[var(--color-muted)] hover:text-red-500 font-mono text-[11px] px-1 hover:underline cursor-pointer border-none bg-transparent">Xóa</button>
                                </div>
                                <div class="flex items-center gap-2 recipient-row">
                                    <input type="checkbox" checked onchange="toggleRecipientInput(this)" class="w-3.5 h-3.5 accent-[var(--color-accent)] cursor-pointer">
                                    <input type="email" name="recipients[]" value="dev.watermelon2602@gmail.com" required placeholder="name@example.com" class="flex-1 px-2.5 py-1.5 text-xs rounded-[6px] border border-[var(--color-rule-2)] bg-[var(--color-paper)] text-[var(--color-ink)] placeholder-[var(--color-muted)] outline-none focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] transition-all">
                                    <button type="button" onclick="removeRecipientRow(this)" class="text-[var(--color-muted)] hover:text-red-500 font-mono text-[11px] px-1 hover:underline cursor-pointer border-none bg-transparent">Xóa</button>
                                </div>
                                <div class="flex items-center gap-2 recipient-row">
                                    <input type="checkbox" checked onchange="toggleRecipientInput(this)" class="w-3.5 h-3.5 accent-[var(--color-accent)] cursor-pointer">
                                    <input type="email" name="recipients[]" value="dev.pineapple.salt@gmail.com" required placeholder="name@example.com" class="flex-1 px-2.5 py-1.5 text-xs rounded-[6px] border border-[var(--color-rule-2)] bg-[var(--color-paper)] text-[var(--color-ink)] placeholder-[var(--color-muted)] outline-none focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] transition-all">
                                    <button type="button" onclick="removeRecipientRow(this)" class="text-[var(--color-muted)] hover:text-red-500 font-mono text-[11px] px-1 hover:underline cursor-pointer border-none bg-transparent">Xóa</button>
                                </div>
                            </div>
                            @error('recipients')
                                <p class="text-xs text-red-500 font-mono mt-1">* {{ $message }}</p>
                            @enderror
                            @error('recipients.*')
                                <p class="text-xs text-red-500 font-mono mt-1">* {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Subject -->
                        <div class="space-y-1.5">
                            <label for="subject" class="block font-mono text-[10px] uppercase tracking-[0.06em] text-[var(--color-muted)] font-semibold">
                                Tiêu đề email
                            </label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" placeholder="Nhập tiêu đề..." class="w-full px-3 py-2.5 text-sm rounded-[6px] border border-[var(--color-rule-2)] bg-[var(--color-paper)] text-[var(--color-ink)] placeholder-[var(--color-muted)] outline-none focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] transition-all">
                            @error('subject')
                                <p class="text-xs text-red-500 font-mono mt-1">* {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div class="space-y-1.5">
                            <label for="content" class="block font-mono text-[10px] uppercase tracking-[0.06em] text-[var(--color-muted)] font-semibold">
                                Nội dung email
                            </label>
                            <textarea id="content" name="content" rows="6" placeholder="Nhập nội dung thư..." class="w-full px-3 py-2.5 text-sm rounded-[6px] border border-[var(--color-rule-2)] bg-[var(--color-paper)] text-[var(--color-ink)] placeholder-[var(--color-muted)] outline-none focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] transition-all resize-y">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="text-xs text-red-500 font-mono mt-1">* {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Action -->
                        <button type="submit" class="w-full py-3 bg-[var(--color-accent)] hover:bg-[var(--color-accent)]/95 text-[var(--color-accent-ink)] font-semibold rounded-[6px] text-xs uppercase tracking-wider transition-colors shadow-sm cursor-pointer flex items-center justify-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                            </svg>
                            <span>Gửi mail quảng bá</span>
                        </button>
                    </form>
                </div>

                <!-- Right: History List -->
                <div class="lg:col-span-7 border border-[var(--color-rule)] bg-[var(--color-paper)] rounded-[10px] overflow-hidden reveal">
                    <div class="px-6 py-5 border-b border-[var(--color-rule)] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs uppercase tracking-wider text-[var(--color-accent)] font-semibold">[ 02 ]</span>
                            <h3 class="font-display font-semibold text-lg text-[var(--color-ink)]">Nhật ký gửi Email</h3>
                        </div>
                        <span class="font-mono text-[10px] text-[var(--color-muted)] bg-[var(--color-paper-2)] border border-[var(--color-rule)] px-2 py-1 rounded-[4px]">
                            Tổng: {{ $logs->total() }}
                        </span>Email
                    </div>

                    @if($logs->isEmpty())
                        <div class="p-12 text-center space-y-3">
                            <svg class="w-10 h-10 text-[var(--color-rule-2)] mx-auto" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                            </svg>
                            <p class="text-xs text-[var(--color-muted)]">Chưa ghi nhận mail nào được gửi đi.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-[var(--color-paper-2)] border-b border-[var(--color-rule)] font-mono text-[10px] uppercase tracking-[0.06em] text-[var(--color-muted)]">
                                        <th class="py-3 px-4">Tiêu đề / Người gửi</th>
                                        <th class="py-3 px-4">Danh sách email</th>
                                        <th class="py-3 px-4 text-center">Tổng số</th>
                                        <th class="py-3 px-4 text-center">Thành công</th>
                                        <th class="py-3 px-4 text-center">Thất bại</th>
                                        <th class="py-3 px-4 text-right">Ngày gửi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[var(--color-rule)]">
                                    @foreach($logs as $log)
                                        <tr class="hover:bg-[var(--color-paper-2)] transition-colors">
                                            <td class="py-4 px-4 font-medium text-[var(--color-ink)]">
                                                <div class="truncate max-w-[150px]" title="{{ $log->subject }}">{{ $log->subject }}</div>
                                                <div class="text-[10px] text-[var(--color-muted)] flex items-center gap-1.5 mt-0.5 font-mono">
                                                    <span>by {{ $log->user ? $log->user->name : 'N/A' }}</span>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 font-mono text-[10px] text-[var(--color-ink-2)]">
                                                @if(is_array($log->recipients))
                                                    <div class="space-y-0.5 max-w-[180px]" title="{{ implode(', ', $log->recipients) }}">
                                                        @foreach($log->recipients as $email)
                                                            <div class="truncate">{{ $email }}</div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-[var(--color-muted)]">N/A</span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-4 text-center font-mono text-[var(--color-ink-2)]">{{ $log->total_recipients }}</td>
                                            <td class="py-4 px-4 text-center font-mono">
                                                <span class="text-green-600 bg-green-500/5 px-2 py-0.5 rounded-[4px] border border-green-500/10 font-semibold">
                                                    {{ $log->sent_success }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 text-center font-mono">
                                                <span class="{{ $log->sent_failed > 0 ? 'text-red-600 bg-red-500/5 border border-red-500/10 font-semibold' : 'text-[var(--color-muted)]' }} px-2 py-0.5 rounded-[4px]">
                                                    {{ $log->sent_failed }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 text-right font-mono text-[var(--color-muted)]">
                                                <div>{{ $log->created_at->format('d/m/Y') }}</div>
                                                <div class="text-[10px] mt-0.5">{{ $log->created_at->format('H:i:s') }}</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Links -->
                        <div class="p-4 border-t border-[var(--color-rule)] flex justify-center text-xs font-mono">
                            {{ $logs->links() }}
                        </div>
                    @endif
                </div>

            </div>
        @endguest

    </main>

    <!-- Signature 8: One Dark Graphite Band (Light -> Dark -> Light rhythm) -->
    <section id="features-section" class="w-full bg-[var(--color-graphite)] text-[var(--color-graphite-ink)] py-16 border-y border-[var(--color-graphite-rule)]">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="space-y-2.5">
                <div class="font-mono text-[10px] uppercase tracking-[0.06em] text-[var(--color-accent)] font-semibold">
                    01 // BẢO MẬT TUYỆT ĐỐI
                </div>
                <h4 class="font-display font-semibold text-lg text-white">Google OAuth</h4>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Xác thực và phân phối trực tiếp thông qua API chính thức của Google, đảm bảo thông tin cá nhân và tài khoản gửi luôn được bảo vệ ở mức tối đa.
                </p>
            </div>
            
            <div class="space-y-2.5">
                <div class="font-mono text-[10px] uppercase tracking-[0.06em] text-[var(--color-accent)] font-semibold">
                    02 // RIÊNG BIỆT & THỐNG KÊ
                </div>
                <h4 class="font-display font-semibold text-lg text-white">Gửi email tuần tự</h4>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Hệ thống phân phối email riêng lẻ giúp tối ưu hóa khả năng gửi vào hộp thư đến, đồng thời ghi nhận kết quả thành công/thất bại cho từng người nhận.
                </p>
            </div>

            <div class="space-y-2.5">
                <div class="font-mono text-[10px] uppercase tracking-[0.06em] text-[var(--color-accent)] font-semibold">
                    03 // NHẬT KÝ CHI TIẾT
                </div>
                <h4 class="font-display font-semibold text-lg text-white">Lịch sử Email Logs</h4>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Toàn bộ lịch sử các chiến dịch gửi đi bao gồm tiêu đề, nội dung và số liệu thống kê được lưu giữ đầy đủ để phục vụ việc tra cứu và phân tích.
                </p>
            </div>
        </div>
    </section>

    <!-- Footer Archetype: Ft2 Inline rule single line -->
    <footer class="w-full max-w-7xl mx-auto px-6 py-8 border-t border-[var(--color-rule)] flex flex-col sm:flex-row items-center justify-between text-xs text-[var(--color-muted)] font-mono">
        <p>&copy; {{ date('Y') }} MailCampaign. Tất cả các quyền được bảo lưu.</p>
        <div class="flex items-center gap-6 mt-4 sm:mt-0">
            <span class="hover:text-[var(--color-ink)] transition-colors cursor-pointer" onclick="openPalette()">⌘K Lệnh nhanh</span>
            <a href="https://laravel.com" target="_blank" class="hover:text-[var(--color-ink)] transition-colors">Laravel 13</a>
            <a href="https://github.com" target="_blank" class="hover:text-[var(--color-ink)] transition-colors">GitHub</a>
        </div>
    </footer>

    <!-- Working ⌘K Command Palette Overlay (Required move 3) -->
    <div id="palette-overlay" class="hidden fixed inset-0 z-50 bg-[var(--color-graphite)]/40 backdrop-blur-sm flex items-start justify-center pt-[15vh]">
        <!-- Backdrop Closer -->
        <div class="absolute inset-0 cursor-pointer" onclick="closePalette()"></div>
        
        <!-- Palette Container -->
        <div class="relative w-full max-w-lg border border-[var(--color-rule)] bg-[var(--color-paper)] rounded-[10px] shadow-2xl overflow-hidden animate-fade-in flex flex-col">
            <!-- Search bar -->
            <div class="flex items-center gap-3 px-4 py-3 border-b border-[var(--color-rule)] bg-[var(--color-paper-2)]">
                <svg class="w-4 h-4 text-[var(--color-muted)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="palette-search" placeholder="Nhập lệnh cần tìm kiếm..." class="w-full bg-transparent text-sm outline-none text-[var(--color-ink)] placeholder-[var(--color-muted)] font-body" oninput="filterPalette()">
                <kbd class="px-1.5 py-0.5 rounded bg-[var(--color-paper)] border border-[var(--color-rule-2)] text-[10px] font-mono text-[var(--color-muted)]">ESC</kbd>
            </div>
            
            <!-- Commands list -->
            <div class="p-2 max-h-72 overflow-y-auto space-y-0.5 font-mono text-xs" id="palette-list">
                @auth
                    <!-- Focus Subject Command -->
                    <button onclick="triggerPaletteAction('focus-subject')" class="w-full text-left px-3 py-2.5 rounded-[6px] hover:bg-[var(--color-accent)] hover:text-[var(--color-accent-ink)] transition-colors flex items-center justify-between group cursor-pointer">
                        <span class="font-medium text-[var(--color-ink)] group-hover:text-inherit">Soạn nội dung quảng bá mới</span>
                        <kbd class="text-[10px] text-[var(--color-muted)] group-hover:text-inherit">S</kbd>
                    </button>
                    <!-- Focus Content Command -->
                    <button onclick="triggerPaletteAction('focus-content')" class="w-full text-left px-3 py-2.5 rounded-[6px] hover:bg-[var(--color-accent)] hover:text-[var(--color-accent-ink)] transition-colors flex items-center justify-between group cursor-pointer">
                        <span class="font-medium text-[var(--color-ink)] group-hover:text-inherit">Soạn nội dung email</span>
                        <kbd class="text-[10px] text-[var(--color-muted)] group-hover:text-inherit">C</kbd>
                    </button>
                    <!-- Scroll to History Command -->
                    <button onclick="triggerPaletteAction('scroll-history')" class="w-full text-left px-3 py-2.5 rounded-[6px] hover:bg-[var(--color-accent)] hover:text-[var(--color-accent-ink)] transition-colors flex items-center justify-between group cursor-pointer">
                        <span class="font-medium text-[var(--color-ink)] group-hover:text-inherit">Xem nhật ký quảng bá</span>
                        <kbd class="text-[10px] text-[var(--color-muted)] group-hover:text-inherit">H</kbd>
                    </button>
                    <!-- Trigger Logout Command -->
                    <button onclick="triggerPaletteAction('logout')" class="w-full text-left px-3 py-2.5 rounded-[6px] hover:bg-red-500 hover:text-white transition-colors flex items-center justify-between group cursor-pointer">
                        <span class="font-medium text-[var(--color-ink)] group-hover:text-inherit">Đăng xuất tài khoản</span>
                        <kbd class="text-[10px] text-[var(--color-muted)] group-hover:text-inherit">Q</kbd>
                    </button>
                @else
                    <!-- Redirect Login Command -->
                    <a href="{{ route('google.login') }}" class="block text-left px-3 py-2.5 rounded-[6px] hover:bg-[var(--color-accent)] hover:text-[var(--color-accent-ink)] transition-colors flex items-center justify-between group cursor-pointer">
                        <span class="font-medium text-[var(--color-ink)] group-hover:text-inherit">Đăng nhập tài khoản Google</span>
                        <kbd class="text-[10px] text-[var(--color-muted)] group-hover:text-inherit">L</kbd>
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- JS Scripts for reveals and working command palette -->
    <script>
        // Setup Reveal animations
        document.addEventListener('DOMContentLoaded', () => {
            const reveals = document.querySelectorAll('.reveal');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if(entry.isIntersecting) {
                        entry.target.classList.add('is-in');
                    }
                });
            }, {
                threshold: 0.05
            });

            reveals.forEach((el) => observer.observe(el));
            
            // Trigger reveals for immediately visible elements
            setTimeout(() => {
                reveals.forEach((el) => {
                    const rect = el.getBoundingClientRect();
                    if (rect.top < window.innerHeight) {
                        el.classList.add('is-in');
                    }
                });
            }, 100);

            // Prevent double form submissions on send email
            const campaignForm = document.getElementById('campaign-form');
            if (campaignForm) {
                campaignForm.addEventListener('submit', () => {
                    const submitBtn = campaignForm.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        setTimeout(() => {
                            submitBtn.disabled = true;
                            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                            const spanText = submitBtn.querySelector('span');
                            if (spanText) {
                                spanText.textContent = 'Đang gửi...';
                            }
                        }, 50);
                    }
                });
            }
        });

        // Command Palette Logic
        const overlay = document.getElementById('palette-overlay');
        const searchInput = document.getElementById('palette-search');
        
        function openPalette() {
            overlay.classList.remove('hidden');
            searchInput.value = '';
            filterPalette();
            setTimeout(() => searchInput.focus(), 50);
        }

        function closePalette() {
            overlay.classList.add('hidden');
        }

        // Global hotkey listening for ⌘K or Ctrl+K and Esc
        window.addEventListener('keydown', (e) => {
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                if (overlay.classList.contains('hidden')) {
                    openPalette();
                } else {
                    closePalette();
                }
            }
            if (e.key === 'Escape') {
                closePalette();
            }
        });

        function filterPalette() {
            const query = searchInput.value.toLowerCase();
            const items = document.querySelectorAll('#palette-list button, #palette-list a');
            items.forEach((item) => {
                const text = item.textContent.toLowerCase();
                if (text.includes(query)) {
                    item.classList.remove('hidden');
                    item.classList.add('flex');
                } else {
                    item.classList.remove('flex');
                    item.classList.add('hidden');
                }
            });
        }

        function triggerPaletteAction(action) {
            closePalette();
            if (action === 'focus-subject') {
                const el = document.getElementById('subject');
                if (el) el.focus();
            } else if (action === 'focus-content') {
                const el = document.getElementById('content');
                if (el) el.focus();
            } else if (action === 'scroll-history') {
                const el = document.getElementById('features-section');
                if (el) el.scrollIntoView({ behavior: 'smooth' });
            } else if (action === 'logout') {
                const el = document.getElementById('logout-form');
                if (el) el.submit();
            }
        }

        // Dynamic Recipient management functions
        function toggleRecipientInput(checkbox) {
            const input = checkbox.nextElementSibling;
            if (checkbox.checked) {
                input.disabled = false;
                input.classList.remove('opacity-50', 'bg-[var(--color-paper-2)]');
                input.classList.add('bg-[var(--color-paper)]');
            } else {
                input.disabled = true;
                input.classList.add('opacity-50', 'bg-[var(--color-paper-2)]');
                input.classList.remove('bg-[var(--color-paper)]');
            }
        }

        function addRecipient() {
            const container = document.getElementById('recipients-container');
            const newRow = document.createElement('div');
            newRow.className = 'flex items-center gap-2 recipient-row';
            newRow.innerHTML = `
                <input type="checkbox" checked onchange="toggleRecipientInput(this)" class="w-3.5 h-3.5 accent-[var(--color-accent)] cursor-pointer">
                <input type="email" name="recipients[]" required placeholder="name@example.com" class="flex-1 px-2.5 py-1.5 text-xs rounded-[6px] border border-[var(--color-rule-2)] bg-[var(--color-paper)] text-[var(--color-ink)] placeholder-[var(--color-muted)] outline-none focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] transition-all">
                <button type="button" onclick="removeRecipientRow(this)" class="text-[var(--color-muted)] hover:text-red-500 font-mono text-[11px] px-1 hover:underline cursor-pointer border-none bg-transparent">Xóa</button>
            `;
            container.appendChild(newRow);
        }

        function removeRecipientRow(button) {
            const row = button.closest('.recipient-row');
            if (row) {
                row.remove();
            }
        }
    </script>
</body>
</html>
