@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 p-10">
    <div class="p-8 max-w-8xl mx-auto">
        <h1 class="text-4xl font-extrabold mb-8 text-gray-800 text-left">Uređivanje termina</h1>
        
        <form action="{{ route('rooms.update', $room->id) }}" method="POST" class="grid grid-cols-2 gap-6 w-full">
            @csrf
            @method('PUT')

            <!-- Prikaz grešaka -->
            @if ($errors->any())
                <div class="col-span-2">
                    <div class="bg-red-500 text-white p-4 rounded-lg">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Naslov -->
            <div class="col-span-2">
                <label for="title" class="block text-lg font-semibold text-gray-700">Naslov</label>
                <input type="text" id="title" name="title" value="{{ old('title', $room->title) }}" 
                       class="mt-2 block w-full p-3 border border-gray-300 rounded-lg shadow-sm text-base focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition" required>
            </div>

            <!-- Opis -->
            <div class="col-span-2">
                <label for="description" class="block text-lg font-semibold text-gray-700">Opis</label>
                <textarea id="description" name="description" 
                          class="mt-2 block w-full p-3 border border-gray-300 rounded-lg shadow-sm text-base focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition" 
                          rows="3">{{ old('description', $room->description) }}</textarea>
            </div>

            <!-- Grad i Lokacija na istoj liniji -->
            <div class="col-span-2 grid grid-cols-2 gap-4">
                <div>
                    <label for="city_id" class="block text-lg font-semibold text-gray-700">Grad</label>
                    <select id="city_id" name="city_id" 
                            class="mt-2 block w-full p-3 border border-gray-300 rounded-lg shadow-sm text-base focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition" required onchange="fetchLocations()">
                        <option value="" disabled>Odaberi grad</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}" 
                                {{ old('city_id', $room->city_id) == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="location_id" class="block text-lg font-semibold text-gray-700">Lokacija</label>
                    <select id="location_id" name="location_id" 
                            class="mt-2 block w-full p-3 border border-gray-300 rounded-lg shadow-sm text-base focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition" required>
                        <option value="" disabled selected>Odaberi lokaciju</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}" 
                                {{ old('location_id', $room->location_id) == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Maksimalan broj igrača -->
            <div class="col-span-2">
                <label for="max_players" class="block text-lg font-semibold text-gray-700">Maksimalan broj igrača</label>
                <input type="number" id="max_players" name="max_players" min="1" 
                       value="{{ old('max_players', $room->max_players) }}" 
                       class="mt-2 block w-full p-3 border border-gray-300 rounded-lg shadow-sm text-base focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition" required>
            </div>

            <!-- Datum i Vrijeme -->
            <div class="col-span-1">
                <label for="date" class="block text-lg font-semibold text-gray-700">Datum</label>
                <input type="date" id="date" name="date" value="{{ old('date', $room->date) }}" 
                       class="mt-2 block w-full p-3 border border-gray-300 rounded-lg shadow-sm text-base focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition" required>
            </div>

            <div class="col-span-1 grid grid-cols-2 gap-4">
                <div>
                    <label for="start_time" class="block text-lg font-semibold text-gray-700">Početak</label>
                    <input type="time" id="start_time" name="start_time" 
                           value="{{ old('start_time', \Carbon\Carbon::createFromFormat('H:i:s', $room->start_time)->format('H:i')) }}" 
                           class="mt-2 block w-full p-3 border border-gray-300 rounded-lg shadow-sm text-base focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition" required>
                </div>
                <div>
                    <label for="end_time" class="block text-lg font-semibold text-gray-700">Kraj</label>
                    <input type="time" id="end_time" name="end_time" 
                           value="{{ old('end_time', \Carbon\Carbon::createFromFormat('H:i:s', $room->end_time)->format('H:i')) }}" 
                           class="mt-2 block w-full p-3 border border-gray-300 rounded-lg shadow-sm text-base focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition" required>
                </div>
            </div>

            <div class="col-span-2 flex justify-between pt-4">
                <button onclick="window.history.back()" type="button" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg shadow-md">
                    Nazad
                </button>
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-10 rounded-lg shadow-lg text-lg">
                    Sačuvaj izmjene
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function fetchLocations() {
    let cityId = document.getElementById('city_id').value;
    let locationSelect = document.getElementById('location_id');
    locationSelect.innerHTML = '<option value="" disabled selected>Učitavanje...</option>';

    fetch(`/locations/by-city?city_id=${cityId}`)
        .then(response => response.json())
        .then(data => {
            locationSelect.innerHTML = '';
            data.forEach(location => {
                let option = document.createElement('option');
                option.value = location.id;
                option.text = location.name;
                locationSelect.appendChild(option);
            });
        })
        .catch(error => {
            locationSelect.innerHTML = '<option value="" disabled>Greška pri učitavanju</option>';
        });
}
</script>
@endsection
