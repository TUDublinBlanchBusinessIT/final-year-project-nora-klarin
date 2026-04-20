<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Placements Map
        </h2>
    </x-slot>

    <div class="space-y-4">

        {{-- Filters --}}
        <div class="flex gap-4">
            <label class="flex items-center gap-2">
                <input type="checkbox" id="filterAvailable" checked>
                <span>Available</span>
            </label>


        {{-- Map --}}
        <div id="map" class="w-full h-[500px] rounded-lg shadow"></div>
    </div>

    <script>
        const placements = @json($placements);

        let map;
        let markers = [];

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 53.3498, lng: -6.2603 }, // Dublin
                zoom: 10,
            });

            renderMarkers();
        }

const placements = @json($placements);

function renderMarkers() {
    markers.forEach(m => m.setMap(null));
    markers = [];

    placements.forEach(p => {
        if (!p.latitude || !p.longitude) return;

        const marker = new google.maps.Marker({
            position: {
                lat: parseFloat(p.latitude),
                lng: parseFloat(p.longitude)
            },
            map: map,
            title: p.location ?? 'Placement',
        });

        const info = new google.maps.InfoWindow({
            content: `
                <div>
                    <strong>${p.location ?? 'Placement #' + p.id}</strong><br/>
                    Status: ${p.status}<br/>
                    Capacity: ${p.capacity ?? 'N/A'}
                </div>
            `
        });

        marker.addListener("click", () => {
            info.open(map, marker);
        });

        markers.push(marker);
    });
}

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('filterAvailable').addEventListener('change', renderMarkers);
        });
    </script>

<script async
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap">
</script>
</x-app-layout>