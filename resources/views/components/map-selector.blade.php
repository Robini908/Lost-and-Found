<!-- Map Selector Component -->
<div x-data="mapSelector({
    address: @entangle('location_address').defer,
    latitude: @entangle('location_lat').defer,
    longitude: @entangle('location_lng').defer,
    apiKey: '{{ config('services.google.maps_api_key') }}'
})"
x-init="initializeMap()"
class="relative">
    <!-- Search Box -->
    <div class="mb-4">
        <div class="relative">
            <input
                type="text"
                x-ref="searchInput"
                placeholder="Search for a location..."
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Map Container -->
    <div class="relative">
        <div x-ref="mapContainer" class="w-full h-[400px] rounded-lg shadow-md"></div>

        <!-- Loading State -->
        <div x-show="loading" class="absolute inset-0 bg-gray-100 bg-opacity-50 flex items-center justify-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
        </div>

        <!-- Current Location Button -->
        <button
            @click="getCurrentLocation"
            class="absolute top-4 right-4 bg-white p-2 rounded-lg shadow-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            title="Use current location">
            <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </button>
    </div>

    <!-- Selected Location Info -->
    <div x-show="address" class="mt-4 p-4 bg-blue-50 rounded-lg">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-blue-900">Selected Location</h4>
                <p x-text="address" class="mt-1 text-sm text-blue-700"></p>
                <div class="mt-1 text-xs text-blue-500">
                    <span x-text="'Lat: ' + latitude"></span>
                    <span class="mx-2">|</span>
                    <span x-text="'Lng: ' + longitude"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('mapSelector', ({ address, latitude, longitude, apiKey }) => ({
        map: null,
        marker: null,
        geocoder: null,
        autocomplete: null,
        loading: true,
        address: address,
        latitude: latitude,
        longitude: longitude,

        async initializeMap() {
            this.loading = true;

            // Load Google Maps API
            await this.loadGoogleMapsAPI();

            // Initialize the map
            this.map = new google.maps.Map(this.$refs.mapContainer, {
                center: { lat: this.latitude || 0, lng: this.longitude || 0 },
                zoom: 12,
                styles: [
                    {
                        featureType: "poi",
                        elementType: "labels",
                        stylers: [{ visibility: "off" }]
                    }
                ],
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false
            });

            // Initialize the marker
            this.marker = new google.maps.Marker({
                map: this.map,
                draggable: true,
                animation: google.maps.Animation.DROP
            });

            // Initialize geocoder
            this.geocoder = new google.maps.Geocoder();

            // Initialize autocomplete
            this.autocomplete = new google.maps.places.Autocomplete(this.$refs.searchInput, {
                types: ['address']
            });

            // Set up event listeners
            this.setupEventListeners();

            // If we have initial coordinates, set them
            if (this.latitude && this.longitude) {
                this.updateMarkerPosition({ lat: parseFloat(this.latitude), lng: parseFloat(this.longitude) });
            }

            this.loading = false;
        },

        setupEventListeners() {
            // Listen for map clicks
            this.map.addListener('click', (e) => {
                this.updateMarkerPosition(e.latLng);
            });

            // Listen for marker drag events
            this.marker.addListener('dragend', () => {
                this.updateMarkerPosition(this.marker.getPosition());
            });

            // Listen for autocomplete selection
            this.autocomplete.addListener('place_changed', () => {
                const place = this.autocomplete.getPlace();
                if (place.geometry) {
                    this.updateMarkerPosition(place.geometry.location);
                    this.map.setCenter(place.geometry.location);
                    this.map.setZoom(17);
                }
            });
        },

        async updateMarkerPosition(latLng) {
            const lat = typeof latLng.lat === 'function' ? latLng.lat() : latLng.lat;
            const lng = typeof latLng.lng === 'function' ? latLng.lng() : latLng.lng;

            this.marker.setPosition(latLng);
            this.map.panTo(latLng);

            // Update the coordinates
            this.latitude = lat.toFixed(6);
            this.longitude = lng.toFixed(6);

            // Get the address for these coordinates
            try {
                const result = await this.geocodePosition(latLng);
                if (result) {
                    this.address = result.formatted_address;
                }
            } catch (error) {
                console.error('Geocoding failed:', error);
            }
        },

        async geocodePosition(latLng) {
            return new Promise((resolve, reject) => {
                this.geocoder.geocode({ location: latLng }, (results, status) => {
                    if (status === 'OK' && results[0]) {
                        resolve(results[0]);
                    } else {
                        reject(status);
                    }
                });
            });
        },

        getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const latLng = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        this.updateMarkerPosition(latLng);
                        this.map.setZoom(17);
                    },
                    (error) => {
                        console.error('Error getting current location:', error);
                    }
                );
            }
        },

        async loadGoogleMapsAPI() {
            return new Promise((resolve, reject) => {
                if (window.google && window.google.maps) {
                    resolve();
                    return;
                }

                const script = document.createElement('script');
                script.src = `https://maps.googleapis.com/maps/api/js?key=${this.apiKey}&libraries=places`;
                script.async = true;
                script.defer = true;
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        }
    }));
});
</script>
