{{-- resources/views/filament/pendaftar/widgets/informasi-penting-widget.blade.php --}}

<x-filament::widget>
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow">
        <div class="p-4 sm:p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-amber-100 dark:bg-amber-900 rounded-lg shrink-0">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    {{ $content['info_heading'] ?? 'Informasi Penting' }}
                </h3>
            </div>

            <div class="space-y-4">
                @foreach (($content['info_items'] ?? []) as $item)
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full font-bold text-sm">
                            {{ $loop->iteration }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">
                                {{ $item['title'] ?? '' }}
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                {!! nl2br(e($item['description'] ?? '')) !!}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            @if (!empty($content['contact_phone']) || !empty($content['contact_email']) || !empty($content['contact_whatsapp']))
                {{-- Contact Info --}}
                <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <div class="flex-1 min-w-0 text-sm">
                            <p class="font-semibold text-gray-900 dark:text-gray-100 mb-1">
                                {{ $content['contact_heading'] ?? 'Kontak Panitia SPMB:' }}
                            </p>
                            <div class="text-gray-700 dark:text-gray-300 space-y-1 break-words">
                                @if (!empty($content['contact_phone']))
                                    <div><span class="font-medium text-gray-900 dark:text-gray-100">Telepon:</span> {{ $content['contact_phone'] }}</div>
                                @endif
                                @if (!empty($content['contact_email']))
                                    <div class="break-all"><span class="font-medium text-gray-900 dark:text-gray-100">Email:</span> {{ $content['contact_email'] }}</div>
                                @endif
                                @if (!empty($content['contact_whatsapp']))
                                    <div><span class="font-medium text-gray-900 dark:text-gray-100">WhatsApp:</span> {{ $content['contact_whatsapp'] }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-filament::widget>
