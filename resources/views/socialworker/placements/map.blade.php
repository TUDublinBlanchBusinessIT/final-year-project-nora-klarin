<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Placements Map
        </h2>
    </x-slot>

    <div class="space-y-4">

        {{-- Filters --}}
        <div class="flex items-center gap-4">
            <label class="flex items-center gap-2">
                <input type="checkbox" id="filterAvailable" checked class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm text-gray-700">Show available placements only</span>
            </label>
        </div>

        {{-- Map --}}
        <div id="map" class="w-full h-[500px] rounded-lg shadow"></div>
    </div>

    @push('scripts')
    <script>
        const placements = @json($placements);
        let map;
        let markers = [];

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 53.3498, lng: -6.2603 },
                zoom: 10,
            });

            renderMarkers();
        }

        function renderMarkers() {
            markers.forEach(marker => marker.setMap(null));
            markers = [];

            const showAvailableOnly = document.getElementById('filterAvailable')?.checked;

            placements.forEach(p => {
                if (!p.latitude || !p.longitude) return;
                if (showAvailableOnly && p.status !== 'available') return;

                const marker = new google.maps.Marker({
                    position: {
                        lat: parseFloat(p.latitude),
                        lng: parseFloat(p.longitude),
                    },
                    map,
                    title: p.location ?? `Placement #${p.id}`,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 8,
                        fillColor: p.status === 'available' ? '#10b981' : p.status === 'emergency' ? '#ef4444' : '#f59e0b',
                        fillOpacity: 1,
                        strokeWeight: 1,
                        strokeColor: '#ffffff',
                    },
                });

                const info = new google.maps.InfoWindow({
                    content: `
                        <div style="font-size:14px; line-height:1.4;">
                            <strong>${p.location ?? `Placement #${p.id}`}</strong><br/>
                            Status: ${p.status ?? 'Unknown'}<br/>
                            Capacity: ${p.capacity ?? 'N/A'}<br/>
                            Occupied: ${p.current_occupancy ?? 'N/A'}<br/>
                            Type: ${p.type ?? 'N/A'}
                        </div>
                    `,
                });

                marker.addListener('click', () => info.open(map, marker));
                markers.push(marker);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('filterAvailable')?.addEventListener('change', renderMarkers);
        });
    </script>

    <script async src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap"></script>
    @endpush
</x-app-layout>