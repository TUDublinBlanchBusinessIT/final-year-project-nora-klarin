<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl leading-tight">🗺️ Find Help Nearby</h2>
                <p class="text-sm opacity-70 mt-1">Find nearby support services and helpful places</p>
            </div>

            <a href="{{ route('child.dashboard') }}"
               class="px-4 py-2 rounded-xl bg-slate-100 text-slate-800 hover:bg-slate-200 text-sm shadow">
                ← Back to dashboard
            </a>
        </div>
    </x-slot>

    <div class="theme-page min-h-screen py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <div class="theme-card rounded-3xl p-6 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-green-600">Nearby Support Services</h3>
                        <p class="text-sm opacity-70 mt-1">
                            Find counselling, Tusla, and family support services near you
                        </p>
                    </div>
                </div>

                <div class="mb-4 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-4 text-blue-900">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <div class="font-semibold text-blue-800">Use your location</div>
                            <p class="text-sm mt-1">
                                Your location is only used to find nearby support services. It is not saved or shared.
                            </p>
                        </div>

                        <button
                            type="button"
                            onclick="enableLocation()"
                            class="inline-flex items-center justify-center rounded-2xl px-4 py-3
                                   font-semibold text-slate-700 bg-white border border-slate-200 shadow-sm
                                   hover:bg-slate-50 hover:border-slate-300 active:scale-[0.98] transition
                                   focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        >
                            📍 Use my location
                        </button>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 mb-4">
                    <button type="button" onclick="searchServices('Tusla')"
                        class="px-4 py-2 rounded-xl bg-blue-100 text-blue-700 text-sm font-semibold hover:bg-blue-200 transition">
                        Tusla
                    </button>

                    <button type="button" onclick="searchServices('counselling')"
                        class="px-4 py-2 rounded-xl bg-pink-100 text-pink-700 text-sm font-semibold hover:bg-pink-200 transition">
                        Counselling
                    </button>

                    <button type="button" onclick="searchServices('child support agency')"
                        class="px-4 py-2 rounded-xl bg-yellow-100 text-yellow-700 text-sm font-semibold hover:bg-yellow-200 transition">
                        Child Agencies
                    </button>

                    <button type="button" onclick="searchServices('family support service')"
                        class="px-4 py-2 rounded-xl bg-green-100 text-green-700 text-sm font-semibold hover:bg-green-200 transition">
                        Family Support
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div class="lg:col-span-2">
                        <div id="map" style="height: 420px; width: 100%; border-radius: 16px;"></div>
                    </div>

                    <div class="theme-card rounded-2xl p-4">
                        <h4 class="font-bold">Service Details</h4>
                        <div id="serviceDetails" class="mt-3 text-sm opacity-80">
                            <div class="theme-card rounded-xl p-4">
                                Click <span class="font-semibold">Use my location</span> to search near you,
                                or use one of the service buttons to search near Dublin city centre.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-4 text-blue-900">
                    <div class="font-bold text-blue-800">Need urgent help?</div>
                    <p class="text-sm mt-1">
                        If you are in immediate danger, contact emergency services or a trusted adult nearby.
                    </p>
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
        let userMarker = null;

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 13,
                center: currentLocation,
            });

            infoWindow = new google.maps.InfoWindow();
            placesService = new google.maps.places.PlacesService(map);
        }

        function enableLocation() {
            const detailsDiv = document.getElementById('serviceDetails');

            if (!navigator.geolocation) {
                showLocationMessage("Geolocation is not supported by this browser. Showing services near Dublin city centre.");
                searchServices('Tusla');
                return;
            }

            detailsDiv.innerHTML = `
                <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 text-sm text-indigo-800">
                    Finding your location...
                </div>
            `;

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    currentLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    map.setCenter(currentLocation);

                    if (userMarker) {
                        userMarker.setMap(null);
                    }

                    userMarker = new google.maps.Marker({
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
        }

        function handleLocationError(error) {
            let message = "Location access was denied. Showing services near Dublin city centre.";

            if (error.code === error.POSITION_UNAVAILABLE) {
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
                    <div class="font-semibold">${place.name ?? 'Unknown service'}</div>
                    <div><span class="font-medium">Address:</span> ${place.vicinity ?? 'Not available'}</div>
                    <div><span class="font-medium">Rating:</span> ${place.rating ?? 'Not available'}</div>
                    <div><span class="font-medium">Type:</span> ${niceType}</div>

                    <a href="${mapsUrl}" target="_blank"
                       class="inline-block mt-3 px-4 py-2 rounded-xl bg-white border border-slate-200 shadow-sm text-slate-700 text-sm font-semibold hover:bg-slate-50 hover:border-slate-300">
                        Open in Google Maps
                    </a>
                </div>
            `;
        }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap" async defer></script>
</x-app-layout>