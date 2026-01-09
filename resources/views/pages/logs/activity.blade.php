@extends('layouts.main')
@section('container')
    {{-- Removed CDN Tailwind link as it should be handled by the layout/build system --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/github.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/json.min.js"></script>
    <style>
        pre code.hljs {
            padding: 1rem;
            border-radius: 0.75rem;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 0.8rem;
            line-height: 1.4;
            background-color: #f8fafc !important; /* slate-50 */
            border: 1px solid #e2e8f0; /* slate-200 */
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            document.querySelectorAll('pre code').forEach((block) => {
                hljs.highlightBlock(block);
            });
        });
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div class="max-w-7xl mx-auto py-6">
        <!-- Filter Section -->
        <div x-data="{ isFilterOpen: true }" class="bg-white rounded-2xl shadow-md border border-navy-50 mb-8 overflow-hidden">
            <div class="p-5 flex justify-between items-center bg-gray-50 border-b border-gray-100 cursor-pointer" @click="isFilterOpen = !isFilterOpen">
                <div class="flex items-center gap-3">
                    <h2 class="text-lg font-bold text-navy-900">🔍 Filter Logs</h2>
                    @if(count(array_filter(request()->all())))
                        <span class="px-2.5 py-0.5 text-xs font-bold bg-tosca-100 text-tosca-700 rounded-full">
                            {{ count(array_filter(request()->all())) }} active
                        </span>
                    @endif
                </div>
                <button class="text-navy-400 hover:text-navy-600 transition-colors">
                    <svg class="w-6 h-6 transform transition-transform" :class="{'rotate-180': !isFilterOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>

            <div x-show="isFilterOpen" class="p-6 bg-white">
                <form method="GET" action="{{ route('logs.activity') }}" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Search Filter -->
                        <div class="space-y-2">
                            <label for="search" class="text-xs font-bold text-navy-400 uppercase">Search</label>
                            <input type="text" name="search" id="search"
                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:ring-2 focus:ring-tosca-300 focus:border-tosca-400 outline-none transition-all"
                                placeholder="Search props..." value="{{ request('search') }}">
                        </div>

                        <!-- Model Filter -->
                        <div class="space-y-2">
                            <label for="model" class="text-xs font-bold text-navy-400 uppercase">Model</label>
                            <select name="model" id="model"
                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:ring-2 focus:ring-tosca-300 focus:border-tosca-400 outline-none transition-all">
                                <option value="">All Models</option>
                                @foreach ($models as $model)
                                    <option value="{{ $model }}" {{ request('model') === $model ? 'selected' : '' }}>
                                        {{ $model }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Event Filter -->
                        <div class="space-y-2">
                            <label for="event" class="text-xs font-bold text-navy-400 uppercase">Event Type</label>
                            <select name="event" id="event"
                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:ring-2 focus:ring-tosca-300 focus:border-tosca-400 outline-none transition-all">
                                <option value="">All Events</option>
                                @foreach ($events as $event)
                                    <option value="{{ $event }}" {{ request('event') === $event ? 'selected' : '' }}>
                                        {{ ucfirst($event) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Subject ID Filter -->
                        <div class="space-y-2">
                            <label for="subject_id" class="text-xs font-bold text-navy-400 uppercase">ID</label>
                            <input type="text" name="subject_id" id="subject_id"
                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:ring-2 focus:ring-tosca-300 focus:border-tosca-400 outline-none transition-all"
                                placeholder="Enter ID..." value="{{ request('subject_id') }}">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-3 pt-2">
                        @if (count(array_filter(request()->all())))
                            <a href="{{ route('logs.activity') }}"
                                class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition-colors">
                                Reset
                            </a>
                        @endif
                        <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-navy-600 text-white font-bold text-sm hover:bg-navy-700 shadow-lg shadow-navy-200 transition-all transform hover:-translate-y-0.5">
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Activity Table -->
        <div class="bg-white rounded-2xl shadow-md border border-navy-50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-navy-400 uppercase tracking-wider">User / Actor</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-navy-400 uppercase tracking-wider">Subject</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-navy-400 uppercase tracking-wider">Changes</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-navy-400 uppercase tracking-wider">Time</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-navy-100 flex items-center justify-center text-navy-600 font-bold text-xs">
                                            {{ substr($log->causer ? $log->causer->name : 'System', 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-semibold text-navy-900">{{ $log->causer ? $log->causer->name : 'System' }}</div>
                                            <div class="text-xs text-navy-400">ID: {{ $log->causer_id ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-medium text-navy-900">
                                            {{ class_basename($log->subject_type) ?? 'N/A' }} 
                                            <span class="text-navy-400">#{{ $log->subject_id }}</span>
                                        </span>
                                        @php
                                            $badgeColor = match($log->event) {
                                                'created' => 'bg-green-100 text-green-700 border-green-200',
                                                'updated' => 'bg-orange-100 text-orange-700 border-orange-200',
                                                'deleted' => 'bg-red-100 text-red-700 border-red-200',
                                                default => 'bg-blue-100 text-blue-700 border-blue-200'
                                            };
                                        @endphp
                                        <span class="inline-flex w-max items-center px-2.5 py-0.5 rounded-md text-xs font-bold border {{ $badgeColor }}">
                                            {{ ucfirst($log->event ?? 'unknown') }}
                                        </span>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 text-sm w-1/2">
                                    <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                        <pre><code class="language-json">{{ json_encode($log->properties, JSON_PRETTY_PRINT) }}</code></pre>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-navy-700">{{ $log->created_at->format('d M Y') }}</span>
                                        <span class="text-xs text-navy-400">{{ $log->created_at->format('H:i:s') }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">No activity logs found</p>
                                        <p class="text-gray-400 text-sm mt-1">Try adjusting your filters</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
@endsection
