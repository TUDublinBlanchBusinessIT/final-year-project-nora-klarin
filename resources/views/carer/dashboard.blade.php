<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between w-full">

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">

                Hi, {{ Auth::user()->name ?? ($user->name ?? 'there') }}

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



            {{-- Welcome --}}

            <div class="rounded-3xl p-6 shadow-sm bg-white border border-gray-100">

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                    <div>

                        <h1 class="text-2xl font-bold text-gray-900">

                            Welcome back, {{ $user->name ?? Auth::user()->name ?? 'Carer' }}

                        </h1>

                        <p class="text-sm text-gray-600 mt-1">

                            Here is an overview of your care information and recent activity.

                        </p>

                    </div>



                    <div class="flex gap-2 flex-wrap">

                        <a href="{{ route('carer.messages.index') }}"

                           class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm hover:bg-indigo-700 shadow">

                            Messages

                        </a>



                        <a href="{{ route('carer.documents.index') }}"

                           class="px-4 py-2 rounded-xl bg-gray-100 text-gray-800 text-sm hover:bg-gray-200">

                            Wellbeing & Documents

                        </a>



                        @if($case)

                            <a href="{{ route('carer.case-file.show', $case->id) }}"

                               class="px-4 py-2 rounded-xl bg-gray-100 text-gray-800 text-sm hover:bg-gray-200">

                                Case File

                            </a>

                        @endif

                    </div>

                </div>

            </div>



            {{-- Summary cards --}}

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">



                <div class="rounded-3xl p-6 shadow-sm bg-white border border-blue-100">

                    <h3 class="text-lg font-semibold text-blue-700">Upcoming appointments</h3>

                    <p class="text-gray-600 mt-1 text-sm">Next 5 scheduled</p>

                    <div class="mt-4 text-3xl font-bold text-gray-900">

                        {{ $appointments->count() ?? 0 }}

                    </div>

                </div>



                <div class="rounded-3xl p-6 shadow-sm bg-white border border-indigo-100">

                    <h3 class="text-lg font-semibold text-indigo-700">Unread messages</h3>

                    <p class="text-gray-600 mt-1 text-sm">From your social worker</p>

                    <div class="mt-4 text-3xl font-bold text-gray-900">

                        {{ $unreadCount ?? 0 }}

                    </div>

                </div>



                <div class="rounded-3xl p-6 shadow-sm bg-white border border-green-100">

                    <h3 class="text-lg font-semibold text-green-700">Case documents</h3>

                    <p class="text-gray-600 mt-1 text-sm">Wellbeing forms & files</p>

                    <div class="mt-4">

                        <a href="{{ route('carer.documents.index') }}"

                           class="inline-block px-4 py-2 rounded-xl bg-green-600 text-white text-sm hover:bg-green-700">

                            Open

                        </a>

                    </div>

                </div>



            </div>



            {{-- Upcoming appointments --}}

            <div class="rounded-3xl p-7 sm:p-8 shadow-sm bg-white border border-indigo-100">

                <div class="flex items-center justify-between">

                    <h3 class="text-2xl font-bold text-indigo-700">Upcoming Appointments</h3>

                    <a href="{{ route('carer.calendar') }}" class="text-sm text-indigo-600 hover:underline">View all →</a>

                </div>



                <div class="mt-6 space-y-3">

                    @forelse($appointments as $appt)

                        <div class="rounded-xl border p-4 hover:bg-gray-50">

                            <div class="flex items-start justify-between gap-3">

                                <div>

                                    <div class="font-medium text-gray-900">

                                        {{ \Carbon\Carbon::parse($appt->start_time)->format('D d M Y, H:i') }}

                                        @if(!empty($appt->end_time))

                                            - {{ \Carbon\Carbon::parse($appt->end_time)->format('H:i') }}

                                        @endif

                                    </div>



                                    @if(!empty($appt->title))

                                        <div class="text-sm font-semibold text-gray-700 mt-1">

                                            {{ $appt->title }}

                                        </div>

                                    @endif



                                    <div class="text-sm text-gray-600 mt-1">

                                        {{ $appt->notes ?? 'No notes provided' }}

                                    </div>



                                    @if(!empty($appt->location))

                                        <div class="text-sm text-gray-500 mt-2">

                                            📍 {{ $appt->location }}

                                        </div>

                                    @endif

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



            {{-- Support Services Map --}}

            <div class="rounded-3xl p-6 shadow-sm bg-white border border-green-100">

                <div class="flex items-center justify-between mb-4">

                    <div>

                        <h3 class="text-lg font-bold text-green-700">Nearby Support Services</h3>

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



        </div>

    </div>



    <script>

        let map;

        let infoWindow;

        let placesService;

        let currentLocation = { lat: 53.3498, lng: -6.2603 };

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



                        userMarker.addListener("click", () => {

                            infoWindow.setContent("You are here");

                            infoWindow.open(map, userMarker);

                        });



                        searchServices('Tusla');

                    },

                    (error) => {

                        handleLocationError(error);

                        searchServices('Tusla');

                    }

                );

            } else {

                showLocationMessage("Geolocation is not supported by this browser. Showing services near Dublin city centre.");

                searchServices('Tusla');

            }

        }



        function handleLocationError(error) {

            let message = "Location access was denied. Showing services near Dublin city centre.";



            if (error.code === error.PERMISSION_DENIED) {

                message = "Location access was denied. Showing services near Dublin city centre.";

            } else if (error.code === error.POSITION_UNAVAILABLE) {

                message = "Your location could not be determined. Showing services near Dublin city centre.";

            } else if (error.code === error.TIMEOUT) {

                message = "Location request timed out. Showing services near Dublin city centre.";

            }



            showLocationMessage(message);

        }



        function showLocationMessage(message) {

            const detailsDiv = document.getElementById('serviceDetails');

            detailsDiv.innerHTML = `

                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">

                    ${message}

                </div>

            `;

        }



        function clearMarkers() {

            markers.forEach(marker => marker.setMap(null));

            markers = [];

        }



        function searchServices(keyword) {

            clearMarkers();



            const request = {

                location: currentLocation,

                radius: 5000,

                keyword: keyword

            };



            placesService.nearbySearch(request, (results, status) => {

                if (status !== google.maps.places.PlacesServiceStatus.OK || !results.length) {

                    document.getElementById('serviceDetails').innerHTML = `

                        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">

                            No ${keyword} services found nearby.

                        </div>

                    `;

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



        function getNiceType(types) {

            if (!types || !types.length) return 'Support service';



            if (types.includes('local_government_office')) return 'Government office';

            if (types.includes('hospital')) return 'Hospital';

            if (types.includes('doctor')) return 'Healthcare service';

            if (types.includes('school')) return 'School';

            if (types.includes('health')) return 'Health service';

            if (types.includes('social_service')) return 'Social service';

            if (types.includes('establishment')) return 'Support service';

            if (types.includes('point_of_interest')) return 'Support service';



            return types[0].replaceAll('_', ' ');

        }



        function showServiceDetails(place) {

            const detailsDiv = document.getElementById('serviceDetails');

            const niceType = getNiceType(place.types);



            const mapsUrl = place.geometry && place.geometry.location

                ? `https://www.google.com/maps/dir/?api=1&destination=${place.geometry.location.lat()},${place.geometry.location.lng()}`

                : '#';



            detailsDiv.innerHTML = `

                <div class="space-y-2">

                    <div class="font-semibold text-gray-900">${place.name ?? 'Unknown service'}</div>

                    <div><span class="font-medium text-gray-700">Address:</span> ${place.vicinity ?? 'Not available'}</div>

                    <div><span class="font-medium text-gray-700">Rating:</span> ${place.rating ?? 'Not available'}</div>

                    <div><span class="font-medium text-gray-700">Type:</span> ${niceType}</div>



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