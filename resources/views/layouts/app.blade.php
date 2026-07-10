<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'MailCampaign — Broadcast Manager')</title>
    
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
            --color-paper: oklch(99% 0.003 256); /* light ice background */
            --color-paper-2: oklch(96.8% 0.006 256); /* clean panel background */
            --color-rule: oklch(91.5% 0.008 256); /* hairline border */
            --color-rule-2: oklch(82.5% 0.015 256); /* focused border */
            --color-neutral: oklch(56% 0.008 250);
            --color-muted: oklch(46% 0.012 250); /* dim text */
            --color-ink: oklch(18% 0.016 256); /* deep graphite text */
            --color-ink-2: oklch(32% 0.014 256); /* secondary text */
            --color-accent: oklch(56% 0.21 256); /* vibrant electric cobalt */
            --color-accent-ink: oklch(98.5% 0.004 250);
            --color-focus: oklch(68% 0.18 256);
            --color-graphite: oklch(14% 0.02 260); /* dark band for contrast structure */
            --color-graphite-ink: oklch(88% 0.006 260);
            --color-graphite-rule: oklch(22% 0.02 260);

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

        html {
            font-size: 20px;
        }

        body {
            background-color: var(--color-paper);
            background-image: radial-gradient(circle at top right, oklch(90% 0.06 256 / 0.3), transparent 50%),
                              radial-gradient(circle at bottom left, oklch(92% 0.04 256 / 0.2), transparent 50%);
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
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-[var(--color-accent)] selection:text-[var(--color-accent-ink)]">

    <!-- Nav Archetype: N13 Inline ⌘K search pill + wordmark + CTA -->
    <nav class="sticky top-0 z-30 w-full border-b border-[var(--color-rule)] bg-[var(--color-paper)]/85 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <div class="w-7 h-7 rounded-[6px] border border-[var(--color-accent)] flex items-center justify-center text-[var(--color-accent)] transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                    </div>
                    <span class="font-display font-semibold text-sm tracking-tight text-[var(--color-ink)]">MailCampaign</span>
                </a>

                @auth
                    <!-- Nav links -->
                    <div class="hidden sm:flex items-center gap-4 border-l border-[var(--color-rule)] pl-4">
                        <a href="{{ route('home') }}" class="text-xs font-mono {{ Route::is('home') ? 'text-[var(--color-accent)] font-semibold' : 'text-[var(--color-ink-2)] hover:text-[var(--color-accent)]' }}">Soạn thư</a>
                        <a href="{{ route('email.logs') }}" class="text-xs font-mono {{ Route::is('email.logs') ? 'text-[var(--color-accent)] font-semibold' : 'text-[var(--color-ink-2)] hover:text-[var(--color-accent)]' }}">Nhật ký</a>
                    </div>
                @endauth
                
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
                    <div class="flex items-center gap-3 bg-[var(--color-paper-2)] border border-[var(--color-rule)] rounded-full pl-2 pr-4 py-1.5 shadow-sm transition-all hover:border-[var(--color-rule-2)] select-none">
                        <!-- User Avatar with Connected Dot -->
                        <div class="relative flex items-center justify-center">
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full border border-[var(--color-rule-2)] object-cover select-none">
                            @else
                                <div class="w-8 h-8 rounded-full bg-[var(--color-accent)]/10 text-[var(--color-accent)] flex items-center justify-center font-semibold text-xs border border-[var(--color-accent)]/20">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <!-- Glowing online dot representing connected with Google API -->
                            <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-500 border-2 border-[var(--color-paper-2)] shadow-[0_0_8px_rgba(16,185,129,0.5)] animate-pulse"></span>
                        </div>
                        
                        <!-- User Info -->
                        <div class="flex flex-col text-left hidden sm:flex">
                            <span class="text-[11px] font-semibold text-[var(--color-ink)] leading-tight max-w-[120px] truncate" title="{{ $user->name }}">{{ $user->name }}</span>
                            <span class="text-[9px] font-mono text-[var(--color-muted)] leading-tight mt-0.5 max-w-[120px] truncate" title="{{ $user->email }}">{{ $user->email }}</span>
                        </div>

                        <!-- Divider line inside pill -->
                        <div class="h-6 w-px bg-[var(--color-rule)] mx-1 hidden sm:block"></div>

                        <!-- Logout Form/Button -->
                        <form action="{{ route('logout') }}" method="POST" id="logout-form" class="inline flex items-center">
                            @csrf
                            <button type="submit" class="text-xs font-mono text-rose-600 hover:text-rose-500 font-semibold border-none bg-transparent cursor-pointer flex items-center gap-1.5 group transition-colors outline-none">
                                <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                </svg>
                                <span class="hidden md:inline text-[11px]">Thoát</span>
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-6 py-12">
        <!-- Flash alerts (Signature 3: Status & Feedback alerts) -->
        @if (session('success'))
            <div class="flash-alert mb-8 p-4 rounded-[6px] bg-emerald-500/5 border border-emerald-500/15 text-emerald-600 text-xs font-mono flex items-center gap-2 reveal">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="flash-alert mb-8 p-4 rounded-[6px] bg-red-500/5 border border-red-500/15 text-red-500 text-xs font-mono flex items-center gap-2 reveal">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Email Log Detail Modal -->
    <div id="detail-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/40 backdrop-blur-sm hidden" onclick="closeDetailModal(event)">
        <div class="relative w-full max-w-2xl border border-[var(--color-rule)] bg-[var(--color-paper)] rounded-[10px] shadow-2xl overflow-hidden animate-fade-in flex flex-col max-h-[85vh]" onclick="event.stopPropagation()">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-[var(--color-rule)] bg-[var(--color-paper-2)]">
                <div class="flex items-center gap-2">
                    <span class="font-mono text-[10px] text-[var(--color-accent)] font-semibold uppercase">[ Chi tiết quảng bá ]</span>
                </div>
                <button onclick="closeDetailModalDirect()" class="text-[var(--color-muted)] hover:text-[var(--color-ink)] font-mono text-[11px] border border-[var(--color-rule)] bg-[var(--color-paper)] px-2 py-0.5 rounded-[4px] cursor-pointer outline-none">
                    ESC Đóng
                </button>
            </div>
            
            <!-- Modal Body (Scrollable) -->
            <div class="p-6 overflow-y-auto space-y-5 text-xs text-[var(--color-ink-2)] font-body">
                <!-- Subject -->
                <div class="space-y-1">
                    <span class="font-mono text-[10px] uppercase text-[var(--color-muted)] font-semibold block">Tiêu đề email</span>
                    <h4 id="modal-subject" class="font-display font-semibold text-base text-[var(--color-ink)]"></h4>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 p-3.5 rounded-[6px] border border-[var(--color-rule)] bg-[var(--color-paper-2)]">
                    <div>
                        <span class="font-mono text-[9px] uppercase text-[var(--color-muted)] block">Người gửi</span>
                        <span id="modal-sender" class="font-semibold text-[var(--color-ink)]"></span>
                    </div>
                    <div>
                        <span class="font-mono text-[9px] uppercase text-[var(--color-muted)] block">Thời gian</span>
                        <span id="modal-time" class="font-mono text-[var(--color-ink)]"></span>
                    </div>
                    <div>
                        <span class="font-mono text-[9px] uppercase text-[var(--color-muted)] block">Trạng thái</span>
                        <span id="modal-status" class="font-semibold"></span>
                    </div>
                    <div>
                        <span class="font-mono text-[9px] uppercase text-[var(--color-muted)] block">Gửi thành công</span>
                        <span id="modal-success" class="text-emerald-600 font-semibold font-mono"></span>
                    </div>
                    <div>
                        <span class="font-mono text-[9px] uppercase text-[var(--color-muted)] block">Gửi thất bại</span>
                        <span id="modal-failed" class="font-mono"></span>
                    </div>
                </div>

                <!-- Recipients -->
                <div class="space-y-1.5">
                    <span class="font-mono text-[10px] uppercase text-[var(--color-muted)] font-semibold block">Danh sách email nhận</span>
                    <div id="modal-recipients" class="flex flex-wrap gap-1.5 max-h-24 overflow-y-auto p-2 border border-[var(--color-rule)] rounded-[6px] bg-[var(--color-paper)] font-mono text-[10px]">
                        <!-- Render tags dynamically -->
                    </div>
                </div>

                <!-- Content Preview -->
                <div class="space-y-1.5">
                    <span class="font-mono text-[10px] uppercase text-[var(--color-muted)] font-semibold block">Xem trước nội dung email</span>
                    <div class="border border-[var(--color-rule)] rounded-[6px] overflow-hidden bg-white">
                        <iframe id="modal-content-frame" class="w-full h-64 border-none bg-white"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Archetype: Ft2 Inline rule single line -->
    <footer class="w-full max-w-7xl mx-auto px-6 py-8 border-t border-[var(--color-rule)] flex flex-col sm:flex-row items-center justify-between text-xs text-[var(--color-muted)] font-mono mt-24">
        <p>&copy; {{ date('Y') }} MailCampaign. Tất cả các quyền được bảo lưu.</p>
        <div class="flex items-center gap-6 mt-4 sm:mt-0">
            <span class="hover:text-[var(--color-ink)] transition-colors cursor-pointer" onclick="openPalette()">⌘K Lệnh nhanh</span>
            <a href="https://laravel.com" target="_blank" class="text-[var(--color-muted)] hover:text-[var(--color-ink)] transition-colors">Laravel 11</a>
            <a href="https://github.com" target="_blank" class="text-[var(--color-muted)] hover:text-[var(--color-ink)] transition-colors">GitHub</a>
        </div>
    </footer>

    <!-- Command Palette Overlay -->
    <div id="palette-overlay" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/40 backdrop-blur-sm hidden" onclick="closePalette()">
        
        <!-- Palette Container -->
        <div class="relative w-full max-w-lg border border-[var(--color-rule)] bg-[var(--color-paper)] rounded-[10px] shadow-2xl overflow-hidden animate-fade-in flex flex-col" onclick="event.stopPropagation()">
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
                    <!-- Nav to Compose -->
                    <button onclick="triggerPaletteAction('nav-compose')" class="w-full text-left px-3 py-2.5 rounded-[6px] hover:bg-[var(--color-accent)] hover:text-[var(--color-accent-ink)] transition-colors flex items-center justify-between group cursor-pointer">
                        <span class="font-medium text-[var(--color-ink)] group-hover:text-inherit">Trang soạn thư</span>
                        <kbd class="text-[10px] text-[var(--color-muted)] group-hover:text-inherit">S</kbd>
                    </button>
                    <!-- Nav to Logs -->
                    <button onclick="triggerPaletteAction('nav-logs')" class="w-full text-left px-3 py-2.5 rounded-[6px] hover:bg-[var(--color-accent)] hover:text-[var(--color-accent-ink)] transition-colors flex items-center justify-between group cursor-pointer">
                        <span class="font-medium text-[var(--color-ink)] group-hover:text-inherit">Trang nhật ký gửi thư</span>
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

            // Auto-hide flash alerts after 5 seconds
            const alerts = document.querySelectorAll('.flash-alert');
            alerts.forEach((alert) => {
                setTimeout(() => {
                    // Smooth transition fade-out
                    alert.style.transition = 'opacity 0.6s ease, transform 0.6s ease, max-height 0.6s ease, margin 0.6s ease, padding 0.6s ease';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    
                    // After transition finishes, collapse layout height
                    setTimeout(() => {
                        alert.style.maxHeight = '0px';
                        alert.style.paddingTop = '0px';
                        alert.style.paddingBottom = '0px';
                        alert.style.marginTop = '0px';
                        alert.style.marginBottom = '0px';
                        alert.style.border = 'none';
                        alert.style.overflow = 'hidden';
                        setTimeout(() => {
                            alert.remove();
                        }, 600);
                    }, 600);
                }, 5000);
            });
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
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                openPalette();
            }
            if (e.key === 'Escape') {
                closePalette();
                if (typeof closeDetailModalDirect === 'function') {
                    closeDetailModalDirect();
                }
            }
        });

        function filterPalette() {
            const query = searchInput.value.toLowerCase().trim();
            const list = document.getElementById('palette-list');
            const items = list.querySelectorAll('button, a');

            items.forEach((item) => {
                const text = item.querySelector('span').textContent.toLowerCase();
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
            if (action === 'nav-compose') {
                window.location.href = "{{ route('home') }}";
            } else if (action === 'nav-logs') {
                window.location.href = "{{ route('email.logs') }}";
            } else if (action === 'logout') {
                const el = document.getElementById('logout-form');
                if (el) el.submit();
            }
        }

        // Modal functions
        const detailModal = document.getElementById('detail-modal');

        function openDetailModal(button) {
            const dataStr = button.getAttribute('data-log');
            if (!dataStr) return;
            try {
                const log = JSON.parse(dataStr);
                
                // Set Subject
                document.getElementById('modal-subject').textContent = log.subject || '(Không có tiêu đề)';
                
                // Set Info Grid
                document.getElementById('modal-sender').textContent = log.sender_name || 'N/A';
                document.getElementById('modal-time').textContent = `${log.created_at_date} ${log.created_at_time}`;
                document.getElementById('modal-success').textContent = log.sent_success;
                document.getElementById('modal-failed').textContent = log.sent_failed;
                
                // Style and set status in detail modal
                const statusEl = document.getElementById('modal-status');
                if (log.status === 'pending') {
                    statusEl.className = 'text-amber-600 font-semibold';
                    statusEl.textContent = 'Chờ gửi';
                } else if (log.status === 'sending') {
                    statusEl.className = 'text-blue-600 font-semibold';
                    statusEl.textContent = 'Đang gửi';
                } else if (log.status === 'completed') {
                    statusEl.className = 'text-emerald-600 font-semibold';
                    statusEl.textContent = 'Hoàn thành';
                } else {
                    statusEl.className = 'text-rose-600 font-semibold';
                    statusEl.textContent = 'Thất bại';
                }
                
                // Style failed count based on value
                const failedEl = document.getElementById('modal-failed');
                if (log.sent_failed > 0) {
                    failedEl.className = 'text-red-600 font-semibold font-mono';
                } else {
                    failedEl.className = 'text-[var(--color-muted)] font-mono';
                }

                // Render Recipients Tags
                const recipientsContainer = document.getElementById('modal-recipients');
                recipientsContainer.innerHTML = '';
                if (Array.isArray(log.recipients) && log.recipients.length > 0) {
                    log.recipients.forEach(item => {
                        const email = typeof item === 'object' && item !== null ? item.email : item;
                        const status = typeof item === 'object' && item !== null ? item.status : 'success';
                        
                        const span = document.createElement('span');
                        span.className = 'px-2 py-0.5 rounded-[4px] border font-mono text-[10px] flex items-center gap-1.5';
                        
                        if (status === 'success') {
                            span.className += ' bg-emerald-50 border-emerald-200 text-emerald-700';
                            span.innerHTML = `<span>${email}</span><span class="w-1 h-1 rounded-full bg-emerald-500"></span>`;
                        } else if (status === 'failed') {
                            span.className += ' bg-rose-50 border-rose-200 text-rose-700';
                            const errorTip = item.error ? ` title="${item.error}"` : '';
                            span.innerHTML = `<span${errorTip}>${email} (Thất bại)</span><span class="w-1 h-1 rounded-full bg-rose-500"></span>`;
                        } else {
                            span.className += ' bg-amber-50 border-amber-200 text-amber-700';
                            span.innerHTML = `<span>${email} (Chờ)</span><span class="w-1 h-1 rounded-full bg-amber-500"></span>`;
                        }
                        
                        recipientsContainer.appendChild(span);
                    });
                } else {
                    recipientsContainer.innerHTML = '<span class="text-[var(--color-muted)] font-mono">Không có người nhận</span>';
                }

                // Render content in Iframe
                const iframe = document.getElementById('modal-content-frame');
                if (iframe) {
                    const doc = iframe.contentDocument || iframe.contentWindow.document;
                    doc.open();
                    const styledContent = `
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <meta charset="utf-8">
                            <style>
                                body {
                                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                                    font-size: 14px;
                                    line-height: 1.6;
                                    color: #334155;
                                    margin: 16px;
                                }
                                h1, h2, h3, h4, h5, h6 {
                                    color: #1e293b;
                                    margin-top: 0;
                                }
                            </style>
                        </head>
                        <body>
                            ${log.content || ''}
                        </body>
                        </html>
                    `;
                    doc.write(styledContent);
                    doc.close();
                }

                // Show Modal
                if (detailModal) {
                    detailModal.classList.remove('hidden');
                }
            } catch (err) {
                console.error("Failed to parse log details:", err);
            }
        }

        function closeDetailModal(event) {
            if (event.target === detailModal) {
                closeDetailModalDirect();
            }
        }

        function closeDetailModalDirect() {
            if (detailModal) {
                detailModal.classList.add('hidden');
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
