<x-app-layout>

<x-slot name="header">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">
                Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }},
                {{ $user->name ?? Auth::user()->name ?? 'Carer' }}
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ now()->format('l, jS F Y') }}
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            {{-- Reminders bell --}}
            <div x-data="{ open: false }" class="relative">
                <button type="button" @click="open = !open"
                        class="relative w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-gray-200 hover:bg-slate-50 transition">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                    @if(isset($reminderCount) && $reminderCount > 0)
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center">
                            {{ $reminderCount }}
                        </span>
                    @endif
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute right-0 top-full mt-2 w-72 bg-white border border-gray-200 rounded-[14px] shadow-xl z-50 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-700">Reminders</p>
                        <p class="text-xs text-gray-400 mt-0.5">Things to check</p>
                    </div>
                    <div class="px-4 py-4">
                        <div class="bg-slate-50 border border-gray-100 rounded-xl p-3 text-center">
                            <p class="text-sm text-gray-500">No reminders at the moment.</p>
                        </div>
                        <button type="button" @click="open = false"
                                class="w-full text-xs text-gray-400 hover:text-gray-600 mt-3 underline">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-slot>

@php
    $latestCheck  = isset($wellbeingChecks) ? $wellbeingChecks->sortByDesc('created_at')->first() : null;
    $latestScore  = $latestCheck ? round($latestCheck->overall_score) : null;
    $wRisk        = $latestCheck->risk_level ?? 'low';
    $nextAppt     = $appointments->sortBy('start_time')->first();
    $scoreColor   = match(true) {
        $latestScore === null   => 'text-gray-300',
        $latestScore >= 70      => 'text-green-600',
        $latestScore >= 45      => 'text-amber-600',
        default                 => 'text-red-600',
    };
    $scoreBorder  = match(true) {
        $latestScore === null   => 'border-gray-200',
        $latestScore >= 70      => 'border-green-200',
        $latestScore >= 45      => 'border-amber-200',
        default                 => 'border-red-200',
    };
@endphp

{{-- ── Stat cards ─────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <a href="{{ route('carer.calendar') }}"
       class="bg-white border border-gray-200 rounded-[14px] p-5 hover:border-indigo-300 hover:shadow-md transition group">
        <p class="text-xs font-medium text-gray-500 mb-1 group-hover:text-indigo-600 transition">Upcoming appointments</p>
        <p class="text-3xl font-bold text-gray-900 tabular-nums">{{ $appointments->count() }}</p>
        <p class="text-xs text-gray-400 mt-1 group-hover:text-indigo-400 transition">View calendar →</p>
    </a>

    <a href="{{ route('carer.messages.index') }}"
       class="bg-white border border-indigo-200 rounded-[14px] p-5 hover:border-indigo-400 hover:shadow-md transition group">
        <p class="text-xs font-medium text-indigo-500 mb-1">Unread messages</p>
        <p class="text-3xl font-bold text-gray-900 tabular-nums">{{ $unreadCount ?? 0 }}</p>
        <p class="text-xs text-indigo-300 mt-1 group-hover:text-indigo-400 transition">Go to messages →</p>
    </a>


    <div class="bg-white border border-gray-200 rounded-[14px] p-5">
        <p class="text-xs font-medium text-gray-500 mb-1">Next appointment</p>
        @if($nextAppt)
            <p class="text-xl font-bold text-gray-900 mt-1">{{ \Carbon\Carbon::parse($nextAppt->start_time)->format('d M') }}</p>
            <p class="text-xs text-gray-500 mt-1 truncate">{{ \Carbon\Carbon::parse($nextAppt->start_time)->format('g:i A') }} · {{ $nextAppt->title ?? 'Appointment' }}</p>
        @else
            <p class="text-xl font-bold text-gray-300 mt-1">None</p>
            <p class="text-xs text-gray-400 mt-1">Contact your social worker</p>
        @endif
    </div>
</div>

{{-- ── Tabs ──────────────────────────────────────────────────────────────── --}}
<div x-data="{ tab: 'appointments' }">

    <div class="flex border-b border-gray-200 mb-5">
        @foreach(['appointments' => 'Appointments', 'services' => 'Nearby services'] as $key => $label)
        <button @click="tab='{{ $key }}'"
                :class="tab==='{{ $key }}' ? 'border-indigo-500 text-indigo-600' : 'text-gray-500 border-transparent hover:text-gray-700'"
                class="py-2.5 px-4 text-sm font-medium border-b-2 transition whitespace-nowrap">
            {{ $label }}
        </button>
        @endforeach
    </div>


    {{-- ── APPOINTMENTS ──────────────────────────────────────────────────── --}}
    <div x-show="tab==='appointments'">
        <div class="bg-white border border-gray-200 rounded-[14px]">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <p class="text-sm font-semibold text-gray-700">Upcoming appointments</p>
                <a href="{{ route('carer.calendar') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View calendar →</a>
            </div>
            @forelse($appointments as $appt)
                <div class="flex items-center gap-4 px-5 py-3.5 border-b border-gray-100 last:border-0 hover:bg-slate-50 transition">
                    <div class="text-center shrink-0 w-10">
                        <p class="text-[10px] text-gray-400 uppercase leading-none">{{ \Carbon\Carbon::parse($appt->start_time)->format('M') }}</p>
                        <p class="text-xl font-bold text-gray-900 leading-tight">{{ \Carbon\Carbon::parse($appt->start_time)->format('d') }}</p>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $appt->title ?? 'Appointment' }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ \Carbon\Carbon::parse($appt->start_time)->format('g:i A') }}
                            @if(!empty($appt->end_time)) – {{ \Carbon\Carbon::parse($appt->end_time)->format('g:i A') }} @endif
                            @if(!empty($appt->location)) · 📍 {{ $appt->location }} @endif
                        </p>
                        @if(!empty($appt->notes))<p class="text-xs text-gray-400 mt-0.5">{{ $appt->notes }}</p>@endif
                    </div>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 ring-1 ring-green-200 shrink-0">Scheduled</span>
                </div>
            @empty
                <div class="px-5 py-12 text-center">
                    <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
                    </svg>
                    <p class="text-sm text-gray-400">No upcoming appointments.</p>
                </div>
            @endforelse
        </div>
    </div>


    {{-- ── NEARBY SERVICES ───────────────────────────────────────────────── --}}
    <div x-show="tab==='services'">
        <div class="bg-white border border-gray-200 rounded-[14px] p-5">
            <div class="mb-4">
                <p class="text-sm font-semibold text-gray-700">Nearby support services</p>
                <p class="text-xs text-gray-400 mt-0.5">Find counselling, Tusla, and child support agencies near you</p>
            </div>
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach(['Tusla' => 'indigo', 'counselling' => 'pink', 'child support agency' => 'amber', 'family support service' => 'green'] as $kw => $clr)
                    <button type="button" onclick="searchServices('{{ $kw }}')"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium bg-{{ $clr }}-50 text-{{ $clr }}-700 border border-{{ $clr }}-200 hover:bg-{{ $clr }}-100 transition">
                        {{ ucfirst($kw) }}
                    </button>
                @endforeach
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2">
                    <div id="map" class="h-96 w-full rounded-xl border border-gray-100"></div>
                </div>
                <div class="bg-slate-50 border border-gray-100 rounded-xl p-4">
                    <p class="text-sm font-semibold text-gray-700 mb-3">Service details</p>
                    <div id="serviceDetails" class="text-sm text-gray-500">Click a search button or map marker to see details.</div>
                </div>
            </div>
        </div>
    </div>

</div>

@if(isset($wellbeingChecks) && $wellbeingChecks->isNotEmpty())
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const sorted = @json($wellbeingChecks->sortBy('created_at')->values());
    const ctx = document.getElementById('carerWellbeingChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: sorted.map(c => new Date(c.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short' })),
                datasets: [{
                    label: 'Overall wellbeing',
                    data: sorted.map(c => c.overall_score),
                    borderColor: '#4F46E5',
                    backgroundColor: '#4F46E510',
                    borderWidth: 2.5,
                    tension: 0.3,
                    pointBackgroundColor: '#4F46E5',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { min: 0, max: 100, grid: { color: '#f1f5f9' }, border: { display: false } },
                    x: { grid: { display: false }, border: { display: false } }
                }
            }
        });
    }
</script>
@endpush
@endif

<script>
    let map, infoWindow, placesService;
    let currentLocation = { lat: 53.3498, lng: -6.2603 };
    let markers = [];

    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), { zoom: 13, center: currentLocation });
        infoWindow = new google.maps.InfoWindow();
        placesService = new google.maps.places.PlacesService(map);
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(pos => {
                currentLocation = { lat: pos.coords.latitude, lng: pos.coords.longitude };
                map.setCenter(currentLocation);
                new google.maps.Marker({ position: currentLocation, map, title: "You" });
                searchServices('Tusla');
            }, () => searchServices('Tusla'));
        } else { searchServices('Tusla'); }
    }

    function clearMarkers() { markers.forEach(m => m.setMap(null)); markers = []; }

    function searchServices(keyword) {
        clearMarkers();
        placesService.nearbySearch({ location: currentLocation, radius: 5000, keyword }, (results, status) => {
            if (status !== google.maps.places.PlacesServiceStatus.OK || !results.length) {
                document.getElementById('serviceDetails').innerHTML = `<p class="text-red-500 text-xs">No ${keyword} services found nearby.</p>`;
                return;
            }
            results.forEach((place, i) => {
                if (!place.geometry) return;
                const marker = new google.maps.Marker({ position: place.geometry.location, map, title: place.name });
                markers.push(marker);
                marker.addListener("click", () => {
                    showServiceDetails(place);
                    infoWindow.setContent(`<strong>${place.name}</strong><br>${place.vicinity ?? ''}`);
                    infoWindow.open(map, marker);
                });
                if (i === 0) showServiceDetails(place);
            });
        });
    }

    function showServiceDetails(place) {
        const types = place.types ?? [];
        let type = 'Support service';
        if (types.includes('local_government_office')) type = 'Government office';
        else if (types.includes('hospital')) type = 'Hospital';
        else if (types.includes('health')) type = 'Health service';
        const mapsUrl = place.geometry ? `https://www.google.com/maps/dir/?api=1&destination=${place.geometry.location.lat()},${place.geometry.location.lng()}` : '#';
        document.getElementById('serviceDetails').innerHTML = `
            <div class="space-y-2">
                <p class="font-semibold text-gray-900 text-sm">${place.name ?? 'Unknown'}</p>
                <p class="text-xs text-gray-500"><span class="font-medium text-gray-700">Address:</span> ${place.vicinity ?? 'Not available'}</p>
                <p class="text-xs text-gray-500"><span class="font-medium text-gray-700">Rating:</span> ${place.rating ?? 'Not rated'}</p>
                <p class="text-xs text-gray-500"><span class="font-medium text-gray-700">Type:</span> ${type}</p>
                <a href="${mapsUrl}" target="_blank" class="inline-flex mt-2 px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-medium hover:bg-indigo-700 transition">Open in Maps →</a>
            </div>`;
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap" async defer></script>

</x-app-layout>
