@extends('layouts.app')

@section('title', 'MailCampaign — Nhật ký gửi thư')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-12">
        <!-- Dedicated Log Table View -->
        <div class="border border-[var(--color-rule)] bg-[var(--color-paper)] rounded-[10px] overflow-hidden reveal w-full shadow-sm">
            <div class="px-6 py-5 border-b border-[var(--color-rule)] flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="font-mono text-xs uppercase tracking-wider text-[var(--color-accent)] font-semibold">[ 02 ]</span>
                    <h3 class="font-display font-semibold text-lg text-[var(--color-ink)]">Nhật ký gửi Email</h3>
                </div>
                <span class="font-mono text-[10px] text-[var(--color-muted)] bg-[var(--color-paper-2)] border border-[var(--color-rule)] px-2 py-1 rounded-[4px]">
                    Tổng: {{ $logs->total() }} Email
                </span>
            </div>

            @if($logs->isEmpty())
                <div class="p-16 text-center space-y-3">
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
                                <th class="py-3 px-4 text-center">Trạng thái</th>
                                <th class="py-3 px-4 text-center">Thành công</th>
                                <th class="py-3 px-4 text-center">Thất bại</th>
                                <th class="py-3 px-4 text-right">Ngày gửi</th>
                                <th class="py-3 px-4 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-rule)]">
                            @foreach($logs as $log)
                                <tr class="hover:bg-[var(--color-paper-2)] transition-colors">
                                    <td class="py-4 px-4 font-medium text-[var(--color-ink)]">
                                        <div class="truncate max-w-[220px]" title="{{ $log->subject }}">{{ $log->subject }}</div>
                                        <div class="text-[10px] text-[var(--color-muted)] flex items-center gap-1.5 mt-0.5 font-mono">
                                            <span>by {{ $log->user ? $log->user->name : 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 font-mono text-[10px] text-[var(--color-ink-2)]">
                                        @if(is_array($log->recipients))
                                            @php
                                                $emailList = array_map(function($item) {
                                                    return is_array($item) ? $item['email'] : $item;
                                                }, $log->recipients);
                                            @endphp
                                            <div class="space-y-0.5 max-w-[280px]" title="{{ implode(', ', $emailList) }}">
                                                @foreach($emailList as $emailVal)
                                                    <div class="truncate">{{ $emailVal }}</div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-[var(--color-muted)]">N/A</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-center font-mono text-[var(--color-ink-2)]">{{ $log->total_recipients }}</td>
                                    <td class="py-4 px-4 text-center">
                                        @if($log->status === 'pending')
                                            <span class="text-amber-700 bg-amber-50 px-2 py-0.5 rounded-[4px] border border-amber-200 font-semibold text-[10px] whitespace-nowrap">Chờ gửi</span>
                                        @elseif($log->status === 'sending')
                                            <span class="text-blue-700 bg-blue-50 px-2 py-0.5 rounded-[4px] border border-blue-200 font-semibold text-[10px] whitespace-nowrap inline-flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                                Đang gửi
                                            </span>
                                        @elseif($log->status === 'completed')
                                            <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-[4px] border border-emerald-200 font-semibold text-[10px] whitespace-nowrap">Hoàn thành</span>
                                        @else
                                            <span class="text-rose-700 bg-rose-50 px-2 py-0.5 rounded-[4px] border border-rose-200 font-semibold text-[10px] whitespace-nowrap">Thất bại</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-center font-mono">
                                        <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-[4px] border border-emerald-200 font-semibold">
                                            {{ $log->sent_success }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center font-mono">
                                        <span class="{{ $log->sent_failed > 0 ? 'text-red-700 bg-red-50 border border-red-200 font-semibold' : 'text-[var(--color-muted)]' }} px-2 py-0.5 rounded-[4px]">
                                            {{ $log->sent_failed }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-right font-mono text-[var(--color-muted)]">
                                        <div>{{ $log->created_at->format('d/m/Y') }}</div>
                                        <div class="text-[10px] mt-0.5">{{ $log->created_at->format('H:i:s') }}</div>
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <button type="button" 
                                                onclick="openDetailModal(this)" 
                                                data-log="{{ json_encode([
                                                    'subject' => $log->subject,
                                                    'content' => $log->content,
                                                    'recipients' => $log->recipients,
                                                    'total_recipients' => $log->total_recipients,
                                                    'sent_success' => $log->sent_success,
                                                    'sent_failed' => $log->sent_failed,
                                                    'status' => $log->status,
                                                    'created_at_date' => $log->created_at->format('d/m/Y'),
                                                    'created_at_time' => $log->created_at->format('H:i:s'),
                                                    'sender_name' => $log->user ? $log->user->name : 'N/A'
                                                ]) }}"
                                                class="text-[var(--color-accent)] hover:underline cursor-pointer border-none bg-transparent font-mono text-[10px]">
                                            [ Chi tiết ]
                                        </button>
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
@endsection
