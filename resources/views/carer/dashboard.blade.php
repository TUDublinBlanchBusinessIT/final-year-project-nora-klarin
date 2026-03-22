<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between w-full">

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">

                👋 Hi {{ Auth::user()->name ?? ($user->name ?? 'there') }}!

            </h2>



            <div class="flex items-center gap-4">

                <div x-data="{ open: false }" class="relative">

                    <button

                        type="button"

                        @click="open = !open"

                        class="relative text-2xl rounded-full px-2 py-1 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-300"

                        aria-label="Open reminders"

                    >

                        🔔

                        @if(isset($reminderCount) && $reminderCount > 0)

                            <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5">

                                {{ $reminderCount }}

                            </span>

                        @endif

                    </button>



                    <div

                        x-show="open"

                        @click.outside="open = false"

                        x-transition

                        class="absolute right-0 mt-2 w-80 rounded-2xl bg-white shadow-xl border border-gray-100 overflow-hidden z-50"

                    >

                        <div class="px-4 py-3 border-b bg-gray-50">

                            <div class="font-extrabold text-gray-800">Reminders</div>

                            <div class="text-xs text-gray-500">Things to check</div>

                        </div>



                        <div class="px-4 py-4 space-y-3">

                            <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">

                                <div class="font-bold text-gray-800">No reminders</div>

                                <div class="text-sm text-gray-600 mt-1">You have no reminders at the moment.</div>

                            </div>



                            <button

                                type="button"

                                @click="open = false"

                                class="w-full text-sm text-gray-600 hover:text-gray-900 underline"

                            >

                                Close

                            </button>

                        </div>

                    </div>

                </div>



                <span class="text-sm text-gray-500">

                    {{ now()->format('l, jS F') }}

                </span>

            </div>

        </div>

    </x-slot>



    <div class="py-10 bg-gradient-to-br from-blue-50 via-pink-50 to-yellow-50">



        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">



            {{-- HERO --}}

            <div class="rounded-2xl p-6">

                <div class="bg-gradient-to-r from-indigo-600 to-sky-500 rounded-2xl p-6 shadow-md text-white">

                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">

                        <div>

                            <p class="text-sm font-medium opacity-90">Welcome back</p>

                            <h1 class="text-3xl font-bold mt-1">{{ $user->name ?? Auth::user()->name ?? 'Carer' }}</h1>

                            <p class="text-sm mt-2 opacity-90">Here’s what’s coming up.</p>

                        </div>



                        <div class="flex gap-3 flex-wrap">

                            <a href="{{ route('carer.calendar') }}"

                               class="px-4 py-2 rounded-xl bg-white/20 text-white hover:bg-white/30 text-sm backdrop-blur">

                                View Calendar

                            </a>



                            <a href="{{ route('carer.messages.index') }}"

                               class="px-4 py-2 rounded-xl bg-white text-gray-900 hover:bg-gray-100 text-sm shadow">

                                Open Messages

                            </a>



                            <a href="{{ route('carer.documents.index') }}"

                               class="px-4 py-2 rounded-xl bg-white text-gray-900 hover:bg-gray-100 text-sm shadow">

                                Documents

                            </a>



                            @if($case)
                                <a href="{{ route('carer.case-file.show', $case->id) }}"
                                   class="px-4 py-2 rounded-xl bg-white text-gray-900 hover:bg-gray-100 text-sm shadow">
                                    View Case File
                                </a>

                            @endif




                        </div>

                    </div>

                </div>

            </div>



            {{-- TOP CARDS --}}

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                <div class="rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-blue-100">

                    <h3 class="text-lg font-extrabold text-blue-700">Upcoming appointments</h3>

                    <p class="text-gray-600 mt-2">Next 5 shown below</p>

                    <div class="mt-4">

                        <div class="text-3xl font-semibold text-gray-900">{{ $appointments->count() ?? 0 }}</div>

                    </div>

                </div>



                <div class="rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-indigo-100">

                    <h3 class="text-lg font-extrabold text-indigo-700">Unread messages</h3>

                    <p class="text-gray-600 mt-2">From your social worker</p>

                    <div class="mt-4">

                        <div class="text-3xl font-semibold text-gray-900">{{ $unreadCount ?? 0 }}</div>

                    </div>

                </div>



                <div class="rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-yellow-100">

                    <h3 class="text-lg font-extrabold text-yellow-700">Alerts</h3>

                    <p class="text-gray-600 mt-2">Latest 5 shown</p>

                    <div class="mt-4">

                        <div class="text-3xl font-semibold text-gray-900">{{ $alerts->count() ?? 0 }}</div>

                    </div>

                </div>



                <div class="rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-pink-100">

                    <h3 class="text-lg font-extrabold text-pink-700">Quick Links</h3>

                    <div class="mt-4 space-y-2">

                        <a href="{{ route('carer.calendar') }}" class="block rounded-2xl bg-pink-50 hover:bg-pink-100 px-4 py-3 font-semibold text-pink-700 transition">

                            📅 View calendar

                        </a>



                        <a href="{{ route('carer.messages.index') }}" class="block rounded-2xl bg-blue-50 hover:bg-blue-100 px-4 py-3 font-semibold text-blue-700 transition">

                            💬 Messages

                        </a>



                        <a href="{{ route('carer.documents.index') }}" class="block rounded-2xl bg-yellow-50 hover:bg-yellow-100 px-4 py-3 font-semibold text-yellow-700 transition">

                            📄 Documents

                        </a>


                        <a href="{{ route('carer.case-file.show', 1) }}" class="block rounded-2xl bg-indigo-50 hover:bg-indigo-100 px-4 py-3 font-semibold text-indigo-700 transition">

                            📁 Case File

                        </a>

                    </div>

                </div>

            </div>



            {{-- MAIN AREA: appointments (left) + alerts (right) --}}

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">



                {{-- Appointments (big left panel) --}}

                <div class="lg:col-span-2 rounded-3xl p-7 sm:p-8 shadow-xl bg-white/95 backdrop-blur border border-indigo-100">

                    <div class="flex items-center justify-between">

                        <h3 class="text-2xl font-extrabold text-indigo-700">Upcoming Appointments</h3>

                        <a href="{{ route('carer.calendar') }}" class="text-sm text-indigo-600 hover:underline">View all →</a>

                    </div>



                    <div class="mt-6 space-y-3">

                        @forelse($appointments as $appt)

                            <div class="rounded-xl border p-4 hover:bg-gray-50">

                                <div class="flex items-start justify-between gap-3">

                                    <div>

                                        <div class="font-medium text-gray-900">

                                            {{ \Carbon\Carbon::parse($appt->starttime)->format('D d M Y, H:i') }}

                                        </div>

                                        <div class="text-sm text-gray-600 mt-1">

                                            {{ $appt->notes ?? 'No notes provided' }}

                                        </div>

                                    </div>

                                    <span class="text-xs px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">

                                        Scheduled

                                    </span>

                                </div>

                            </div>

                        @empty

                            <div class="rounded-xl border border-dashed p-8 text-center text-gray-600">

                                No upcoming appointments.

                            </div>

                        @endforelse

                    </div>

                </div>



                {{-- Alerts (right) --}}

                <div class="rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-indigo-100">

                    <h3 class="text-lg font-extrabold text-indigo-700">🗂️ Recent Alerts</h3>

                    <p class="text-sm text-gray-600 mt-1">Your latest alerts</p>



                    <div class="mt-4 space-y-3">

                        @forelse($alerts as $a)

                            <div class="rounded-2xl border overflow-hidden hover:bg-gray-50 transition">

                                <div class="p-4">

                                    <div class="flex items-start justify-between gap-3">

                                        <div>

                                            <div class="text-sm font-semibold text-gray-900">{{ $a->title ?? 'Update' }}</div>

                                            <div class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($a->createdat ?? $a->created_at)->diffForHumans() }}</div>

                                        </div>



                                        <span class="text-xs px-2 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-100 whitespace-nowrap">Recent</span>

                                    </div>



                                    <div class="text-sm text-gray-600 mt-2">

                                        {{ \Illuminate\Support\Str::limit($a->description ?? $a->desc ?? '', 140) }}

                                    </div>



                                    <div class="mt-3 flex gap-3">

                                        <a href="{{ route('carer.messages.index') }}" class="text-sm text-indigo-600 hover:underline">Message social worker →</a>

                                        <a href="{{ route('carer.calendar') }}" class="text-sm text-gray-600 hover:underline">Check calendar →</a>

                                    </div>

                                </div>

                            </div>

                        @empty

                            <div class="rounded-xl border border-dashed p-8 text-center text-gray-600">No alerts.</div>

                        @endforelse

                    </div>

                </div>

            </div>



{{-- Bottom cards --}}

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">



    <div class="rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-blue-100">

        <div class="flex items-center justify-between">

            <h3 class="text-lg font-extrabold text-blue-700">📅 Calendar</h3>

            <a href="{{ route('carer.calendar') }}" class="text-sm text-blue-600 hover:underline">

                Open →

            </a>

        </div>



        <p class="text-gray-600 mt-2">Your next upcoming events</p>



        <div class="mt-4 space-y-3">

            @forelse($appointments->take(3) as $appt)

                <div class="rounded-2xl bg-blue-50 border border-blue-100 px-4 py-3">

                    <div class="font-semibold text-gray-900">

                        {{ $appt->title ?? 'Appointment' }}

                    </div>



                    <div class="text-sm text-gray-600 mt-1">

                        {{ \Carbon\Carbon::parse($appt->start_time)->format('D d M, H:i') }}

                    </div>



                    @if(!empty($appt->location))

                        <div class="text-xs text-gray-500 mt-1">

                            📍 {{ $appt->location }}

                        </div>

                    @endif

                </div>

            @empty

                <div class="rounded-xl border border-dashed p-6 text-center text-gray-600">

                    No upcoming events

                </div>

            @endforelse

        </div>

    </div>



    <div class="rounded-3xl p-6 shadow-lg bg-gradient-to-br from-yellow-50 to-pink-50 border border-yellow-100">

        <h3 class="text-lg font-extrabold text-pink-700">🌈 Something Positive</h3>

        <p class="text-gray-700 mt-2">“You don’t have to do everything. Just one small step.”</p>

    </div>



</div>




        </div>

    </div>
    
{{-- Support Services Map --}}

<div class="mt-10 rounded-3xl p-6 shadow-lg bg-white/95 backdrop-blur border border-green-100">

    <div class="flex items-center justify-between mb-4">

        <div>

            <h3 class="text-lg font-extrabold text-green-700">🗺️ Nearby Support Services</h3>

            <p class="text-sm text-gray-600 mt-1">Find counselling, Tusla, and child support agencies near you</p>

        </div>

    </div>



    <div class="flex flex-wrap gap-2 mb-4">

        <button type="button" onclick="searchServices('Tusla')"

            class="px-4 py-2 rounded-xl bg-blue-100 text-blue-700 text-sm font-semibold hover:bg-blue-200">

            Tusla

        </button>



        <button type="button" onclick="searchServices('counselling')"

            class="px-4 py-2 rounded-xl bg-pink-100 text-pink-700 text-sm font-semibold hover:bg-pink-200">

            Counselling

        </button>



        <button type="button" onclick="searchServices('child support agency')"

            class="px-4 py-2 rounded-xl bg-yellow-100 text-yellow-700 text-sm font-semibold hover:bg-yellow-200">

            Child Agencies

        </button>



        <button type="button" onclick="searchServices('family support service')"

            class="px-4 py-2 rounded-xl bg-green-100 text-green-700 text-sm font-semibold hover:bg-green-200">

            Family Support

        </button>

    </div>



    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <div class="lg:col-span-2">

            <div id="map" style="height: 420px; width: 100%; border-radius: 16px;"></div>

        </div>



        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">

            <h4 class="font-bold text-gray-800">Service Details</h4>

            <div id="serviceDetails" class="mt-3 text-sm text-gray-600">

                Click a marker or search button to view service details.

            </div>

        </div>

    </div>

</div>



<script>

    let map;

    let infoWindow;

    let placesService;

    let currentLocation = { lat: 53.3498, lng: -6.2603 }; // Dublin fallback

    let markers = [];



    function initMap() {

        map = new google.maps.Map(document.getElementById("map"), {

            zoom: 13,

            center: currentLocation,

        });



        infoWindow = new google.maps.InfoWindow();

        placesService = new google.maps.places.PlacesService(map);



        if (navigator.geolocation) {

            navigator.geolocation.getCurrentPosition(

                (position) => {

                    currentLocation = {

                        lat: position.coords.latitude,

                        lng: position.coords.longitude

                    };



                    map.setCenter(currentLocation);



                    const userMarker = new google.maps.Marker({

                        position: currentLocation,

                        map: map,

                        title: "Your Location",

                    });



                    infoWindow.setPosition(currentLocation);

                    infoWindow.setContent("You are here");

                    infoWindow.open(map);



                    userMarker.addListener("click", () => {

                        infoWindow.setContent("You are here");

                        infoWindow.open(map, userMarker);

                    });



                    searchServices('Tusla');

                },

                () => {

                    document.getElementById('serviceDetails').innerHTML =

                        '<p class="text-red-600">Location access denied. Showing services near Dublin city centre.</p>';

                    searchServices('Tusla');

                }

            );

        } else {

            searchServices('Tusla');

        }

    }



    function clearMarkers() {

        markers.forEach(marker => marker.setMap(null));

        markers = [];

    }



    function searchServices(keyword) {

        clearMarkers();



        document.getElementById('serviceDetails').innerHTML =

            `<p class="text-gray-500">Searching for <strong>${keyword}</strong> services...</p>`;



        const request = {

            location: currentLocation,

            radius: 5000,

            keyword: keyword

        };



        placesService.nearbySearch(request, (results, status) => {

            if (status !== google.maps.places.PlacesServiceStatus.OK || !results.length) {

                document.getElementById('serviceDetails').innerHTML =

                    `<p class="text-red-600">No ${keyword} services found nearby.</p>`;

                return;

            }



            results.forEach((place, index) => {

                if (!place.geometry || !place.geometry.location) return;



                const marker = new google.maps.Marker({

                    position: place.geometry.location,

                    map: map,

                    title: place.name

                });



                markers.push(marker);



                marker.addListener("click", () => {

                    showServiceDetails(place);

                    infoWindow.setContent(`

                        <div style="min-width:180px">

                            <strong>${place.name}</strong><br>

                            ${place.vicinity ?? 'Address not available'}

                        </div>

                    `);

                    infoWindow.open(map, marker);

                });



                if (index === 0) {

                    showServiceDetails(place);

                }

            });

        });

    }



    function showServiceDetails(place) {

        const detailsDiv = document.getElementById('serviceDetails');



        const mapsUrl = place.geometry && place.geometry.location

            ? `https://www.google.com/maps/dir/?api=1&destination=${place.geometry.location.lat()},${place.geometry.location.lng()}`

            : '#';



        detailsDiv.innerHTML = `

            <div class="space-y-2">

                <div class="font-semibold text-gray-900">${place.name ?? 'Unknown service'}</div>

                <div><span class="font-medium text-gray-700">Address:</span> ${place.vicinity ?? 'Not available'}</div>

                <div><span class="font-medium text-gray-700">Rating:</span> ${place.rating ?? 'Not available'}</div>

                <div><span class="font-medium text-gray-700">Type:</span> ${(place.types && place.types.length) ? place.types[0] : 'Not available'}</div>



                <a href="${mapsUrl}" target="_blank"

                   class="inline-block mt-3 px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">

                    Open in Google Maps

                </a>

            </div>

        `;

    }

</script>



<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap" async defer></script>


</x-app-layout>