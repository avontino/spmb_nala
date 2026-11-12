<x-filament::widget>
    @php
        $type = $content['announcement_type'] ?? 'info';
        $styles = match ($type) {
            'warning' => [
                'wrapper' => 'bg-amber-50 border-amber-200 text-amber-950',
                'iconWrap' => 'bg-amber-100 text-amber-700',
                'title' => 'text-amber-950',
                'body' => 'text-amber-900',
                'badge' => 'bg-amber-200 text-amber-950',
                'badgeLabel' => 'Perhatian',
            ],
            'success' => [
                'wrapper' => 'bg-emerald-50 border-emerald-200 text-emerald-950',
                'iconWrap' => 'bg-emerald-100 text-emerald-700',
                'title' => 'text-emerald-950',
                'body' => 'text-emerald-900',
                'badge' => 'bg-emerald-200 text-emerald-950',
                'badgeLabel' => 'Pengumuman Resmi',
            ],
            'danger' => [
                'wrapper' => 'bg-red-50 border-red-200 text-red-950',
                'iconWrap' => 'bg-red-100 text-red-700',
                'title' => 'text-red-950',
                'body' => 'text-red-900',
                'badge' => 'bg-red-200 text-red-950',
                'badgeLabel' => 'Penting',
            ],
            default => [
                'wrapper' => 'bg-blue-50 border-blue-200 text-blue-950',
                'iconWrap' => 'bg-blue-100 text-blue-700',
                'title' => 'text-blue-950',
                'body' => 'text-blue-900',
                'badge' => 'bg-blue-200 text-blue-950',
                'badgeLabel' => 'Pengumuman',
            ],
        };
    @endphp

    <div class="rounded-xl border shadow-sm p-4 sm:p-5 {{ $styles['wrapper'] }}">
        <div class="flex items-start gap-3.5">
            <div class="p-2.5 rounded-xl shrink-0 {{ $styles['iconWrap'] }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $styles['badge'] }}">
                        {{ $styles['badgeLabel'] }}
                    </span>
                    @if (!empty($content['announcement_title']))
                        <h3 class="text-base font-bold {{ $styles['title'] }}">
                            {{ $content['announcement_title'] }}
                        </h3>
                    @endif
                </div>

                @if (!empty($content['announcement_body']))
                    <p class="text-sm leading-relaxed {{ $styles['body'] }}">
                        {!! nl2br(e($content['announcement_body'])) !!}
                    </p>
                @endif
            </div>
        </div>
    </div>
</x-filament::widget>
