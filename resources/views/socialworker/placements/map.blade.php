<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Placements</h2>
            <div class="flex rounded-lg border border-gray-200 overflow-hidden">
                <button id="tab-list" onclick="switchTab('list')"
                        class="px-4 py-2 text-sm font-medium transition bg-indigo-600 text-white">
                    List view
                </button>
                <button id="tab-map" onclick="switchTab('map')"
                        class="px-4 py-2 text-sm font-medium transition bg-white text-gray-600 hover:bg-gray-50 border-l border-gray-200">
                    Map view
                </button>
            </div>
        </div>
    </x-slot>

    {{-- ── LIST VIEW ──────────────────────────────────────────────── --}}
    <div id="view-list" class="space-y-6">

        {{-- Summary stat cards — styled to match dashboard --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Total placements — neutral --}}
            <div class="rounded-xl p-5 bg-white border border-gray-100">
                <p class="text-sm text-gray-500 mb-1">Total placements</p>
                <p class="text-3xl font-semibold text-gray-800">{{ $placements->count() }}</p>
            </div>

            {{-- Occupied — amber/warning when high utilisation --}}
            @php $utilPct = $totalCap > 0 ? round(($totalOcc / $totalCap) * 100) : 0; @endphp
            <div class="rounded-xl p-5 {{ $utilPct >= 80 ? 'bg-red-50 border border-red-100' : 'bg-amber-50 border border-amber-100' }}">
                <p class="text-sm {{ $utilPct >= 80 ? 'text-red-700' : 'text-amber-700' }} mb-1">Currently occupied</p>
                <p class="text-3xl font-semibold {{ $utilPct >= 80 ? 'text-red-800' : 'text-amber-800' }}">{{ $totalOcc }}</p>
                <p class="text-xs {{ $utilPct >= 80 ? 'text-red-500' : 'text-amber-500' }} mt-1">{{ $utilPct }}% utilisation</p>
            </div>

            {{-- Available — green when space exists, red when none --}}
            <div class="rounded-xl p-5 {{ $available > 0 ? 'bg-emerald-50 border border-emerald-100' : 'bg-red-50 border border-red-100' }}">
                <p class="text-sm {{ $available > 0 ? 'text-emerald-700' : 'text-red-700' }} mb-1">Available now</p>
                <p class="text-3xl font-semibold {{ $available > 0 ? 'text-emerald-800' : 'text-red-800' }}">{{ $available }}</p>
                <p class="text-xs {{ $available > 0 ? 'text-emerald-500' : 'text-red-500' }} mt-1">
                    {{ $available > 0 ? 'spaces free' : 'no spaces available' }}
                </p>
            </div>
        </div>

        {{-- Filters — matching system style (same as cases index) --}}
        <div class="flex flex-wrap items-center gap-3">
            <select id="filterType" onchange="applyListFilters()"
                    class="text-sm border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-700 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                <option value="">All types</option>
                @foreach($placements->pluck('type')->unique()->filter()->sort() as $type)
                    <option value="{{ strtolower($type) }}">{{ $type }}</option>
                @endforeach
            </select>

            <select id="filterStatus" onchange="applyListFilters()"
                    class="text-sm border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-700 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                <option value="">All statuses</option>
                <option value="active">Active</option>
                <option value="under_review">Under review</option>
                <option value="inactive">Inactive</option>
            </select>

            <label class="flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox" id="filterAvailable" onchange="applyListFilters()"
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm text-gray-700">Space available</span>
            </label>

            <span id="listCount" class="text-sm text-gray-400 ml-auto"></span>
        </div>

        {{-- Placement cards --}}
        <div id="placementList" class="space-y-3">
            @forelse($placements as $placement)
                @php
                    $occ        = $placement->current_occupancy ?? 0;
                    $cap        = $placement->capacity ?? 0;
                    $avail      = max(0, $cap - $occ);
                    $pct        = $cap > 0 ? round(($occ / $cap) * 100) : 0;
                    $isExpiring = $expiringSoon->has($placement->id);
                    $daysLeft   = $placement->end_date
                                    ? (int) now()->diffInDays($placement->end_date, false)
                                    : null;

                    $barColor = match(true) {
                        $placement->status === 'inactive' => 'bg-gray-300',
                        $pct >= 100                       => 'bg-red-400',
                        $pct >= 75                        => 'bg-amber-400',
                        default                           => 'bg-emerald-500',
                    };
                    $statusClass = match($placement->status) {
                        'active'       => 'bg-emerald-50 text-emerald-800',
                        'under_review' => 'bg-amber-50 text-amber-800',
                        default        => 'bg-gray-100 text-gray-500',
                    };
                @endphp
                <div class="placement-card bg-white border border-gray-100 rounded-xl p-4 shadow-sm"
                     data-type="{{ strtolower($placement->type ?? '') }}"
                     data-status="{{ $placement->status }}"
                     data-available="{{ $avail > 0 ? '1' : '0' }}"
                     style="{{ $placement->status === 'inactive' ? 'opacity:0.55' : '' }}">

                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div class="flex items-center flex-wrap gap-2">
                            <span class="font-medium text-gray-900">{{ $placement->location }}</span>
                            @if($placement->type)
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md">
                                    {{ $placement->type }}
                                </span>
                            @endif
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $statusClass }}">
                                {{ str_replace('_', ' ', ucfirst($placement->status)) }}
                            </span>
                            @if($isExpiring && $daysLeft !== null)
                                <span class="text-xs bg-red-50 text-red-700 px-2 py-0.5 rounded-full font-medium">
                                    Ends in {{ $daysLeft }} day{{ $daysLeft === 1 ? '' : 's' }}
                                </span>
                            @endif
                        </div>
                        <span class="text-xs text-gray-400 whitespace-nowrap shrink-0">
                            Since {{ $placement->start_date?->format('d M Y') ?? '—' }}
                        </span>
                    </div>

                    <p class="text-xs text-gray-500 mb-3">
                        Carer: {{ $placement->carer?->name ?? '—' }}
                        @if($placement->notes)
                            · <span class="italic">{{ Str::limit($placement->notes, 80) }}</span>
                        @endif
                    </p>

                    <div class="flex items-center gap-3">
                        <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                            <div class="{{ $barColor }} h-1.5 rounded-full transition-all"
                                 style="width: {{ min($pct, 100) }}%"></div>
                        </div>
                        <span class="text-xs text-gray-500 whitespace-nowrap">{{ $occ }} / {{ $cap }} occupied</span>
                        @if($placement->status !== 'inactive')
                            <span class="text-xs font-medium whitespace-nowrap {{ $avail > 0 ? 'text-emerald-700' : 'text-gray-400' }}">
                                {{ $avail > 0 ? $avail . ' available' : 'Full' }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-10">No placements recorded yet.</p>
            @endforelse
        </div>
    </div>

    {{-- ── MAP VIEW ────────────────────────────────────────────────── --}}
    <div id="view-map" class="space-y-4 hidden">

        <div class="flex flex-wrap items-center gap-4">
            <label class="flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox" id="filterActive" checked
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="flex items-center gap-1 text-sm text-gray-700">
                    <span class="inline-block w-3 h-3 rounded-full bg-emerald-500"></span> Active
                </span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox" id="filterReview" checked
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="flex items-center gap-1 text-sm text-gray-700">
                    <span class="inline-block w-3 h-3 rounded-full bg-amber-400"></span> Under review
                </span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox" id="filterInactive"
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="flex items-center gap-1 text-sm text-gray-700">
                    <span class="inline-block w-3 h-3 rounded-full bg-gray-400"></span> Inactive
                </span>
            </label>
        </div>

        <div id="map" class="w-full h-[520px] rounded-xl shadow-sm border border-gray-100"></div>
    </div>

    @push('scripts')
    <script>
        function switchTab(tab) {
            const isList = tab === 'list';
            document.getElementById('view-list').classList.toggle('hidden', !isList);
            document.getElementById('view-map').classList.toggle('hidden',  isList);

            document.getElementById('tab-list').className = isList
                ? 'px-4 py-2 text-sm font-medium transition bg-indigo-600 text-white'
                : 'px-4 py-2 text-sm font-medium transition bg-white text-gray-600 hover:bg-gray-50 border-l border-gray-200';
            document.getElementById('tab-map').className = isList
                ? 'px-4 py-2 text-sm font-medium transition bg-white text-gray-600 hover:bg-gray-50 border-l border-gray-200'
                : 'px-4 py-2 text-sm font-medium transition bg-indigo-600 text-white';

            if (!isList && window.googleMap) {
                google.maps.event.trigger(window.googleMap, 'resize');
            }
        }

        function applyListFilters() {
            const type    = document.getElementById('filterType').value.toLowerCase();
            const status  = document.getElementById('filterStatus').value;
            const availOnly = document.getElementById('filterAvailable').checked;
            const cards   = document.querySelectorAll('.placement-card');
            let shown     = 0;

            cards.forEach(card => {
                const matchType   = !type   || card.dataset.type   === type;
                const matchStatus = !status || card.dataset.status === status;
                const matchAvail  = !availOnly || card.dataset.available === '1';
                const visible     = matchType && matchStatus && matchAvail;
                card.style.display = visible ? '' : 'none';
                if (visible) shown++;
            });

            document.getElementById('listCount').textContent =
                shown + ' placement' + (shown === 1 ? '' : 's');
        }

        document.addEventListener('DOMContentLoaded', () => applyListFilters());

        const mappable = @json($mappable);
        const markerMap = {};
        const colorMap = { active: '#10b981', under_review: '#f59e0b', inactive: '#94a3b8' };

        function makeIcon(status) {
            return {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 9,
                fillColor: colorMap[status] ?? '#94a3b8',
                fillOpacity: 1,
                strokeWeight: 2,
                strokeColor: '#ffffff',
            };
        }

        function initMap() {
            const gmap = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 53.3498, lng: -6.2603 },
                zoom: 10,
            });
            window.googleMap = gmap;

            mappable.forEach(p => {
                if (!p.latitude || !p.longitude) return;
                const avail = typeof p.capacity === 'number'
                    ? Math.max(0, p.capacity - (p.current_occupancy ?? 0))
                    : '—';

                const marker = new google.maps.Marker({
                    position: { lat: parseFloat(p.latitude), lng: parseFloat(p.longitude) },
                    map: gmap,
                    title: p.location,
                    icon: makeIcon(p.status),
                });

                const info = new google.maps.InfoWindow({
                    content: `<div style="font-size:13px;line-height:1.6;min-width:160px">
                        <div style="font-weight:600;margin-bottom:4px">${p.location ?? 'Placement #' + p.id}</div>
                        <div><span style="color:#6b7280">Type:</span> ${p.type ?? '—'}</div>
                        <div><span style="color:#6b7280">Status:</span> ${(p.status ?? '').replace('_', ' ')}</div>
                        <div><span style="color:#6b7280">Capacity:</span> ${p.capacity ?? '—'}</div>
                        <div><span style="color:#6b7280">Occupied:</span> ${p.current_occupancy ?? 0}</div>
                        <div><span style="color:#6b7280">Available:</span> ${avail}</div>
                    </div>`,
                });

                marker.addListener('click', () => info.open(gmap, marker));
                markerMap[p.id] = { marker, status: p.status };
            });

            ['filterActive', 'filterReview', 'filterInactive'].forEach(id => {
                document.getElementById(id)?.addEventListener('change', applyMapFilters);
            });
        }

        function applyMapFilters() {
            const show = {
                active:       document.getElementById('filterActive').checked,
                under_review: document.getElementById('filterReview').checked,
                inactive:     document.getElementById('filterInactive').checked,
            };
            Object.values(markerMap).forEach(({ marker, status }) => {
                marker.setMap((show[status] ?? false) ? window.googleMap : null);
            });
        }
    </script>

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap&loading=async"
        async defer>
    </script>
    @endpush
</x-app-layout>
