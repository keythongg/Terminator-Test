@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 p-4 sm:p-10">
    <div class="p-4 sm:p-8 max-w-8xl mx-auto">
        <h1 class="text-3xl sm:text-4xl font-extrabold mb-6 sm:mb-8 text-gray-800 text-left pt-8">Kreiranje termina</h1>
        
        <form action="{{ route('rooms.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 w-full">
            @csrf
            
            <!-- Naslov -->
            <div class="col-span-1 sm:col-span-2">
                <label for="title" class="block text-md sm:text-lg font-semibold text-gray-700">Naslov</label>
                <input type="text" id="title" name="title" 
                    class="mt-1 sm:mt-2 block w-full p-2 sm:p-3 border border-gray-300 rounded-lg shadow-sm text-sm sm:text-base focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition" 
                    placeholder="Unesite naslov termina" required>
            </div>

            <!-- Opis -->
            <div class="col-span-1 sm:col-span-2">
                <label for="description" class="block text-md sm:text-lg font-semibold text-gray-700">Opis</label>
                <textarea id="description" name="description" 
                    class="mt-1 sm:mt-2 block w-full p-2 sm:p-3 border border-gray-300 rounded-lg shadow-sm text-sm sm:text-base focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition" 
                    rows="3" placeholder="Dodajte opis termina..."></textarea>
            </div>

            <!-- Grad i Lokacija u istoj liniji -->
            <div class="col-span-1 sm:col-span-1">
                <label for="city_id" class="block text-md sm:text-lg font-semibold text-gray-700">Grad</label>
                <select id="city_id" name="city_id" 
                    class="mt-1 sm:mt-2 block w-full p-2 sm:p-3 border border-gray-300 rounded-lg shadow-sm text-sm sm:text-base focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition" required onchange="fetchLocations()">
                    <option value="" disabled selected>Odaberi grad</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-span-1 sm:col-span-1">
                <label for="location_id" class="block text-md sm:text-lg font-semibold text-gray-700">Lokacija</label>
                <select id="location_id" name="location_id" 
                    class="mt-1 sm:mt-2 block w-full p-2 sm:p-3 border border-gray-300 rounded-lg shadow-sm text-sm sm:text-base focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition" required>
                    <option value="" disabled selected>Odaberi lokaciju</option>
                </select>
            </div>

            <!-- Maksimalan broj igrača -->
            <div class="col-span-1 sm:col-span-1">
                <label for="max_players" class="block text-md sm:text-lg font-semibold text-gray-700">Maksimalan broj igrača</label>
                <input type="number" id="max_players" name="max_players" min="1" 
                    class="mt-1 sm:mt-2 block w-full p-2 sm:p-3 border border-gray-300 rounded-lg shadow-sm text-sm sm:text-base focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition" 
                    required>
            </div>

            <!-- Datum -->
            <div class="col-span-1">
                <label for="date" class="block text-md sm:text-lg font-semibold text-gray-700">Datum</label>
                <input type="date" id="date" name="date" 
                    class="mt-1 sm:mt-2 block w-full p-2 sm:p-3 border border-gray-300 rounded-lg shadow-sm text-sm sm:text-base focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition" 
                    required>
            </div>

            <!-- Početak, Kraj i Lozinka u istoj liniji -->
            <div class="col-span-2 grid grid-cols-3 gap-4">
                <div>
                    <label for="start_time" class="block text-md sm:text-lg font-semibold text-gray-700">Početak</label>
                    <input type="time" id="start_time" name="start_time" 
                        class="mt-1 sm:mt-2 block w-full p-2 sm:p-3 border border-gray-300 rounded-lg shadow-sm text-sm sm:text-base focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition" 
                        required>
                </div>
                <div>
                    <label for="end_time" class="block text-md sm:text-lg font-semibold text-gray-700">Kraj</label>
                    <input type="time" id="end_time" name="end_time" 
                        class="mt-1 sm:mt-2 block w-full p-2 sm:p-3 border border-gray-300 rounded-lg shadow-sm text-sm sm:text-base focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition" 
                        required>
                </div>
                <div>
                    <label for="password" class="block text-md sm:text-lg font-semibold text-gray-700">Lozinka (opciono)</label>
                    <input type="password" id="password" name="password" 
                        class="mt-1 sm:mt-2 block w-full p-2 sm:p-3 border border-gray-300 rounded-lg shadow-sm text-sm sm:text-base focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition"
                        placeholder="Dodajte lozinku (opciono)">
                </div>
            </div>

            <!-- Dugme za kreiranje -->
            <div class="col-span-2 flex justify-between pt-4">
                <button onclick="window.history.back()" type="button" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg shadow-md">
                    Nazad
                </button>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-10 rounded-lg shadow-lg text-lg">
                    Napravi termin
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
