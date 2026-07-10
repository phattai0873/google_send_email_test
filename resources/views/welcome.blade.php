@extends('layouts.app')

@section('title', 'MailCampaign — Broadcast Manager')

@push('styles')
    <!-- Quill WYSIWYG Editor Styles -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <style>
        /* Custom styling to align Quill with Cobalt theme */
        .ql-toolbar.ql-snow {
            border: none !important;
            border-bottom: 1px solid var(--color-rule-2) !important;
            background-color: var(--color-paper-2) !important;
            padding: 6px 12px !important;
            display: flex;
            flex-wrap: wrap;
            gap: 2px;
        }
        .ql-container.ql-snow {
            border: none !important;
            background-color: var(--color-paper) !important;
            font-family: 'Inter', sans-serif !important;
            font-size: 0.875rem !important;
        }
        .ql-editor {
            min-height: 200px !important;
            max-height: 400px !important;
            overflow-y: auto;
            color: var(--color-ink) !important;
            line-height: 1.6 !important;
            padding: 12px 16px !important;
        }
        .ql-editor.ql-blank::before {
            color: var(--color-muted) !important;
            font-style: normal !important;
            left: 16px !important;
            opacity: 0.8;
        }
        /* Style Quill buttons for Cobalt focus */
        .ql-snow .ql-stroke {
            stroke: var(--color-ink-2) !important;
        }
        .ql-snow .ql-fill {
            fill: var(--color-ink-2) !important;
        }
        .ql-snow .ql-picker {
            color: var(--color-ink-2) !important;
        }
        .ql-snow.ql-toolbar button:hover .ql-stroke,
        .ql-snow .ql-toolbar button:hover .ql-stroke,
        .ql-snow.ql-toolbar button.ql-active .ql-stroke,
        .ql-snow .ql-toolbar button.ql-active .ql-stroke {
            stroke: var(--color-accent) !important;
        }
    </style>
@endpush

@section('content')

        @guest
            <!-- Guest State: Hero Section (Signature 1) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center min-h-[60vh] mb-24">
                
                <!-- Left: Headline & Call-to-action -->
                <div class="lg:col-span-6 space-y-6 text-left reveal">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-[var(--color-rule)] bg-[var(--color-paper-2)]">
                        <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-accent)] animate-ping"></span>
                        <span class="font-mono text-[9px] uppercase tracking-wider text-[var(--color-ink-2)] font-medium">Bản phát hành Beta v1.0.0</span>
                    </div>
                    
                    <h1 class="font-display font-bold text-4xl md:text-5xl tracking-tight text-[var(--color-ink)] leading-[1.1]">
                        Gửi chiến dịch email quảng bá chuyên nghiệp.
                    </h1>
                    
                    <p class="text-sm text-[var(--color-muted)] leading-relaxed max-w-lg">
                        MailCampaign là giải pháp tối giản, hỗ trợ kết nối trực tiếp với tài khoản Google để thực hiện chiến dịch email hàng loạt của bạn thông qua Gmail API chính thức.
                    </p>

                    <div class="pt-4">
                        <a href="{{ route('google.login') }}" class="inline-flex items-center gap-3 px-6 py-3.5 bg-[var(--color-accent)] hover:bg-[var(--color-accent)]/95 text-[var(--color-accent-ink)] font-semibold rounded-[6px] text-xs uppercase tracking-wider transition-colors shadow-sm cursor-pointer border border-transparent outline-none">
                            <span>Kết nối Google & Bắt đầu</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
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
            <!-- Authenticated State: Split Compose Layout (Signature 7) -->
            <div class="max-w-6xl mx-auto border border-[var(--color-rule)] bg-[var(--color-paper-2)] p-6 md:p-8 rounded-[10px] space-y-6 reveal w-full shadow-sm mb-24">
                <div class="flex items-center gap-2 border-b border-[var(--color-rule)] pb-4">
                    <span class="font-mono text-xs uppercase tracking-wider text-[var(--color-accent)] font-semibold">[ 01 ]</span>
                    <h3 class="font-display font-semibold text-lg text-[var(--color-ink)]">Soạn EMAIL quảng bá</h3>
                </div>

                <form action="{{ route('email.send') }}" method="POST" id="campaign-form" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    @csrf
                    
                    <!-- Left Column: Recipients List (col-span-5) -->
                    <div class="lg:col-span-5 space-y-4 border-b lg:border-b-0 lg:border-r border-[var(--color-rule)] pb-6 lg:pb-0 lg:pr-8">
                        <div class="flex items-center justify-between">
                            <label class="block font-mono text-[10px] uppercase tracking-[0.06em] text-[var(--color-muted)] font-semibold">
                                Danh sách người nhận
                            </label>
                            <button type="button" onclick="addRecipient()" class="text-[10px] font-mono text-[var(--color-accent)] hover:underline cursor-pointer border-none bg-transparent outline-none">
                                + Thêm email
                            </button>
                        </div>
                        
                        <div id="recipients-container" class="space-y-3 max-h-[380px] overflow-y-auto pr-2">
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

                    <!-- Right Column: Subject, Content, & Submit (col-span-7) -->
                    <div class="lg:col-span-7 space-y-5">
                        <!-- Subject -->
                        <div class="space-y-1.5">
                            <label for="subject" class="block font-mono text-[10px] uppercase tracking-[0.06em] text-[var(--color-muted)] font-semibold">
                                Tiêu đề email
                            </label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" placeholder="Nhập tiêu đề..." class="w-full px-3 py-2.5 text-sm rounded-[6px] border border-[var(--color-rule-2)] bg-[var(--color-paper)] text-[var(--color-ink)] placeholder-[var(--color-muted)] outline-none focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] transition-all font-body">
                            @error('subject')
                                <p class="text-xs text-red-500 font-mono mt-1">* {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div class="space-y-1.5">
                            <label class="block font-mono text-[10px] uppercase tracking-[0.06em] text-[var(--color-muted)] font-semibold">
                                Nội dung email
                            </label>
                            
                            <!-- Hidden input to store compiled html content -->
                            <input type="hidden" name="content" id="content" value="{{ old('content') }}">
                            
                            <!-- Quill editor wrapper (styled to match Cobalt theme) -->
                            <div class="editor-wrapper rounded-[6px] border border-[var(--color-rule-2)] overflow-hidden bg-[var(--color-paper)] flex flex-col focus-within:border-[var(--color-accent)] focus-within:ring-1 focus-within:ring-[var(--color-accent)] transition-all">
                                <div id="quill-editor" class="min-h-[220px]"></div>
                            </div>

                            @error('content')
                                <p class="text-xs text-red-500 font-mono mt-1">* {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Action -->
                        <button type="submit" class="w-full py-3 bg-[var(--color-accent)] hover:bg-[var(--color-accent)]/95 text-[var(--color-accent-ink)] font-semibold rounded-[6px] text-xs uppercase tracking-wider transition-colors shadow-sm cursor-pointer flex items-center justify-center gap-2 outline-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                            </svg>
                            <span>Gửi mail quảng bá</span>
                        </button>
                    </div>
                </form>
            </div>
        @endguest

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
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        let quill = null;

        document.addEventListener('DOMContentLoaded', () => {
            // Initialize Quill WYSIWYG Editor
            const quillContainer = document.getElementById('quill-editor');
            if (quillContainer) {
                quill = new Quill('#quill-editor', {
                    theme: 'snow',
                    placeholder: 'Viết nội dung email tại đây...',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            [{ 'header': [1, 2, 3, false] }],
                            ['clean']
                        ]
                    }
                });

                // Load initial content from hidden input if present
                const hiddenContentInput = document.getElementById('content');
                if (hiddenContentInput && hiddenContentInput.value) {
                    quill.root.innerHTML = hiddenContentInput.value;
                }
            }

            // Prevent double form submissions on send email
            const campaignForm = document.getElementById('campaign-form');
            if (campaignForm) {
                campaignForm.addEventListener('submit', () => {
                    // Copy Quill content to hidden input before submitting
                    const hiddenContent = document.getElementById('content');
                    if (hiddenContent && quill) {
                        hiddenContent.value = quill.root.innerHTML;
                    }

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

        // Intercept global trigger palette to support focusing editor
        const originalTriggerAction = triggerPaletteAction;
        triggerPaletteAction = function(action) {
            if (action === 'focus-subject') {
                const el = document.getElementById('subject');
                if (el) el.focus();
            } else if (action === 'focus-content') {
                if (quill) {
                    quill.focus();
                } else {
                    const el = document.getElementById('quill-editor') || document.getElementById('content');
                    if (el) el.focus();
                }
            } else {
                originalTriggerAction(action);
            }
        };

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
@endpush
