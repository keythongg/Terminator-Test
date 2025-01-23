@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12">
    <div class="flex justify-between items-center pt-20 mb-8">
        <h1 class="text-4xl font-bold">Termini</h1>

        <!-- Dugme za otvaranje drawer-a (Dodaj novi termin) -->
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-full shadow-lg transition-all"
                type="button"
                onclick="openDrawer()">
            + Dodaj novi termin
        </button>

    </div>

      <!-- Toaster Container -->
      <div id="toast-container" class="fixed bottom-5 right-5 z-50 space-y-4"></div>

   
      @if ($rooms->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach ($rooms as $room)
            <div id="room-{{ $room->id }}" class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all overflow-hidden relative data-status="{{ $room->status }}" ">
                <!-- Prikaz slike lokacije -->
                @if ($room->location && $room->location->image)
                    <img src="{{ $room->location->image }}" alt="{{ $room->location->name }}"
                         class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-500">Nema slike</span>
                    </div>
                @endif

                <!-- Ikonica zaključano/otključano -->
                <div class="absolute top-4 right-4">
                    <div class="p-2 rounded-full shadow-md bg-white bg-opacity-80 flex items-center justify-center" >
                        <i class="fas {{ $room->password ? 'fa-lock' : 'fa-lock-open' }} text-gray-600"></i>
                    </div>
                </div>

                <div class="p-6">
                <h2 class="text-xl font-semibold">{{ $room->title }}</h2>
                <p class="text-gray-600 mt-2"><strong>Lokacija:</strong> {{ $room->location->name }}</p>
                <p class="text-gray-600"><strong>Datum:</strong> {{ \Carbon\Carbon::parse($room->date)->format('d.m.Y') }}</p>
                <p class="text-gray-600"><strong>Vrijeme:</strong> {{ \Carbon\Carbon::parse($room->start_time)->format('H:i') }}</p>
                <p class="text-sm mt-2 {{ $room->status == 'zavrsen' ? 'text-red-500' : 'text-green-500' }}">
                    Status: {{ ucfirst($room->status) }}
                </p>

                    <!-- Dugmići -->
                    <div class="mt-6 flex items-center justify-between">
                        @if (auth()->id() === $room->owner_id)
                            <div class="flex space-x-3">
                                <a href="{{ route('rooms.edit', $room->id) }}"
                                   class="text-gray-700 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-100 transition-all flex items-center">
                                    <i class="fas fa-edit mr-1"></i> Uredi
                                </a>
                                <form action="{{ route('rooms.destroy', $room->id) }}" method="POST"
                                      onsubmit="return confirm('Da li ste sigurni da želite obrisati ovaj termin?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-500 border border-red-300 px-4 py-2 rounded-lg hover:bg-red-50 transition-all flex items-center">
                                        <i class="fas fa-trash-alt mr-1"></i> Obriši
                                    </button>
                                </form>
                            </div>
                        @endif

                        @if ($room->players->contains(auth()->id()))
    <a href="{{ route('rooms.enter', $room->id) }}"
       class="text-blue-500 border border-blue-300 px-4 py-2 rounded-lg hover:bg-blue-50 transition-all flex items-center">
        <i class="fas fa-door-open mr-1"></i> Uđi u sobu
    </a>
@else
    @if ($room->players->count() >= $room->max_players)
        <button disabled class="text-gray-400 border border-gray-300 px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-users mr-1"></i> Soba je puna
        </button>
    @else
        @if ($room->password)
            <!-- Otvori modal za unos lozinke ako soba ima lozinku -->
            <button onclick="openModal({{ $room->id }})"
                    class="text-blue-500 border border-blue-300 px-4 py-2 rounded-lg hover:bg-blue-50 transition-all flex items-center">
                <i class="fas fa-sign-in-alt mr-1"></i> Pridruži se
            </button>
        @else
            <!-- Direktno se pridruži ako soba nema lozinku -->
            <form action="{{ route('rooms.join', $room->id) }}" method="POST">
                @csrf
                <button type="submit"
                        class="text-blue-500 border border-blue-300 px-4 py-2 rounded-lg hover:bg-blue-50 transition-all flex items-center">
                    <i class="fas fa-sign-in-alt mr-1"></i> Pridruži se
                </button>
            </form>
        @endif
    @endif
@endif

                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <p class="text-center text-gray-500">Trenutno nema dostupnih termina.</p>
@endif
</div>


<!-- Drawer za kreiranje termina -->
<div id="drawer-form" 
     class="fixed top-[4rem] left-0 z-40 h-[calc(100vh-4rem)] p-6 overflow-y-auto transition-transform -translate-x-full bg-white w-96 shadow-lg" 
     tabindex="-1">
    <h5 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm14-7.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Z"></path>
        </svg>
        Novi Termin
    </h5>

    <button type="button" 
            onclick="closeDrawer()" 
            class="absolute top-2.5 right-2.5 text-gray-400 hover:bg-gray-200 rounded-lg p-1.5">
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l12 12M13 1L1 13"></path>
        </svg>
    </button>

    <!-- Forma za kreiranje termina -->
    <form method="POST" action="{{ route('rooms.store') }}" class="space-y-6">
    @csrf
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Naslov</label>
        <input type="text" id="title" name="title" value="{{ old('title') }}" class="border border-gray-300 rounded w-full p-2.5 focus:ring-2 focus:ring-blue-500" required>
        @error('title')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Opis</label>
        <textarea id="description" name="description" rows="3" class="border border-gray-300 rounded w-full p-2.5">{{ old('description') }}</textarea>
        @error('description')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Grad i Lokacija na istoj liniji -->
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="city_id" class="block text-sm font-medium text-gray-700">Grad</label>
            <select id="city_id" name="city_id" class="border border-gray-300 rounded w-full p-2.5" required onchange="fetchLocations()">
                <option value="" disabled selected>Odaberi grad</option>
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="location_id" class="block text-sm font-medium text-gray-700">Lokacija</label>
            <select id="location_id" name="location_id" class="border border-gray-300 rounded w-full p-2.5" required>
                <option value="" disabled selected>Odaberi lokaciju</option>
            </select>
        </div>
    </div>

    <!-- Datum i vrijeme -->
    <div class="space-y-6">
        <div class="mb-4">
            <label for="date" class="block text-sm font-medium text-gray-700">Datum</label>
            <input type="date" id="date" name="date" required 
                   class="border border-gray-300 text-sm rounded-lg w-full p-2.5 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="start-time" class="block text-sm font-medium text-gray-700">Početak</label>
                <input type="time" id="start-time" name="start_time" value="00:00" required 
                       class="border border-gray-300 text-sm rounded-lg w-full p-2.5 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="end-time" class="block text-sm font-medium text-gray-700">Kraj</label>
                <input type="time" id="end-time" name="end_time" value="00:00" required 
                       class="border border-gray-300 text-sm rounded-lg w-full p-2.5 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
    </div>

    <!-- MAX IGRAČI -->
    <div>
        <label for="max_players" class="block text-sm font-medium text-gray-700">Maksimalan broj igrača</label>
        <input type="number" id="max_players" name="max_players" min="1" class="border border-gray-300 rounded w-full p-2.5 focus:ring-blue-500 focus:border-blue-500" required>
    </div>

    <!-- Lozinka (opcionalno) -->
    <div class="mb-4">
        <label for="password" class="block text-gray-700 font-bold mb-2">Lozinka (opciono)</label>
        <input type="password" id="password" name="password" class="border border-gray-300 rounded w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Unesite lozinku (ako želite)">
        @error('password')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="w-full bg-blue-600 text-white p-2.5 rounded-lg shadow hover:bg-blue-700">
        Kreiraj termin
    </button>
</form>
</div>



<!-- Modal za unos lozinke -->
<div id="passwordModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-96 p-6">
        <h2 class="text-xl font-bold mb-4">Unesite lozinku za pristup</h2>
        <form id="passwordForm" method="POST">
            @csrf
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-bold mb-2">Lozinka</label>
                <input type="password" id="password" name="password" class="border border-gray-300 rounded w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <p id="errorMessage" class="text-red-500 text-sm mt-2 hidden"></p>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Odustani
                </button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Pristupi
                </button>
            </div>
        </form>
    </div>
</div>





<!-- Modal za recenzije -->
@foreach ($needsReview as $room)
<div id="reviewModal-{{ $room->id }}" class="fixed inset-0 bg-gray-900 bg-opacity-90 flex justify-center items-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden relative">
        <!-- Hero pozadina sa slikom lokacije -->
        <div class="relative">
            @if ($room->location && $room->location->image)
                <img src="{{ $room->location->image }}" alt="{{ $room->location->name }}" class="w-full h-60 object-cover">
            @else
                <div class="w-full h-60 bg-gray-300 flex items-center justify-center">
                    <span class="text-gray-500 text-2xl">Nema slike lokacije</span>
                </div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-black/50 flex justify-between items-center px-6 py-4">
                <h2 class="text-3xl font-bold text-white shadow-md">{{ $room->title }}</h2>
                <button onclick="closeReviewModal('{{ $room->id }}')" class="text-white hover:text-gray-300 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Informacije o terminu -->
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div>
                    <p class="text-sm text-gray-500 uppercase">Lokacija</p>
                    <p class="font-semibold text-gray-800 text-lg">{{ $room->location->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 uppercase">Datum i Vrijeme</p>
                    <p class="font-semibold text-gray-800 text-lg">{{ \Carbon\Carbon::parse($room->date)->format('d.m.Y') }} od {{ \Carbon\Carbon::parse($room->start_time)->format('H:i') }}</p>
                </div>
            </div>

            <h3 class="text-2xl font-bold text-gray-800 mb-6">Recenzije igrača</h3>

            <!-- Forma za recenzije svih igrača -->
            <form action="{{ route('ratings.storeMultiple') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="room_id" value="{{ $room->id }}">

                <!-- Lista igrača -->
                <div class="space-y-4">
                    @foreach ($room->players as $player)
                        @if ($player->id !== auth()->id()) <!-- Ne prikazuje vlastiti profil -->
                            <div class="flex items-center space-x-6 bg-gray-50 p-4 rounded-xl shadow-lg hover:shadow-xl transition">
                                <!-- Slika igrača -->
                                <div>
                                    <div class="relative w-16 h-16 rounded-full overflow-hidden border-4 border-blue-500 shadow-xl flex items-center justify-center bg-gray-200">
                                        @if ($player->profile_photo_path)
                                            <img src="{{ asset('storage/' . $player->profile_photo_path) }}" 
                                                 alt="{{ $player->name }}" 
                                                 class="w-full h-full object-cover">
                                        @else
                                            <span class="text-gray-700 text-xl font-bold">
                                                {{ strtoupper(substr($player->name, 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Informacije o igraču -->
                                <div class="flex-1">
                                    <p class="font-bold text-gray-800 text-lg">{{ $player->name }}</p>
                                    <p class="text-sm text-gray-500">Pridružio se: {{ $player->created_at->format('d.m.Y') }}</p>
                                </div>

                                <!-- Ocjena -->
                                <div>
                                    <select name="ratings[{{ $player->id }}][rating]" required class="border rounded-lg p-2 w-28 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                        <option value="" disabled selected>Ocjena</option>
                                        @for ($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <!-- Dodana polja za rated_id i room_id -->
                                <input type="hidden" name="ratings[{{ $player->id }}][rated_id]" value="{{ $player->id }}">
                                <input type="hidden" name="ratings[{{ $player->id }}][room_id]" value="{{ $room->id }}">
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Dugme za slanje svih recenzija -->
                <button type="submit" class="bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800 text-white px-6 py-3 rounded-lg w-full font-semibold shadow-lg transition">
                    Pošalji sve recenzije
                </button>
            </form>
        </div>
    </div>
</div>
@endforeach








<script>
    let selectedRoomId = null;

    function openModal(roomId) {
    selectedRoomId = roomId;
    const modal = document.getElementById('passwordModal');
    const form = document.getElementById('passwordForm');
    const errorMessage = document.getElementById('errorMessage');

    form.action = `/rooms/${roomId}/check-password`;  // Postavlja akciju forme za proveru lozinke
    errorMessage.classList.add('hidden');  // Sakrij grešku ako postoji
    modal.classList.remove('hidden');  // Prikaži modal
}


    function closeModal() {
        const modal = document.getElementById('passwordModal');
        modal.classList.add('hidden'); // Sakrij modal
    }

    document.getElementById('passwordForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const errorMessage = document.getElementById('errorMessage');

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': formData.get('_token'),
        },
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Ako je lozinka tačna, automatski izvrši pridruživanje sobi
            fetch(`/rooms/${selectedRoomId}/join`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                },
            }).then(() => {
                window.location.href = `/rooms/${selectedRoomId}/enter`;
            });
        } else {
            errorMessage.textContent = data.message || 'Pogrešna lozinka.';
            errorMessage.classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Došlo je do greške:', error);
    });
});


    document.addEventListener('DOMContentLoaded', function () {
        @if (session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif
        @if (session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    });

    function showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toast-container');
        const toast = document.createElement('div');

        const iconClass = type === 'success' ? 'text-green-500 bg-green-100' : 'text-red-500 bg-red-100';

        toast.className = `flex items-center w-full max-w-xs p-4 bg-white rounded-lg shadow-lg`;

        toast.innerHTML = `
            <div class="inline-flex items-center justify-center w-8 h-8 rounded-lg ${iconClass}">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-times-circle'}"></i>
            </div>
            <div class="ms-3 text-sm font-normal">${message}</div>
        `;

        toastContainer.appendChild(toast);
        setTimeout(() => {
            toast.remove();
        }, 4000);
    }
    

    function openDrawer() {
    const drawer = document.getElementById('drawer-form');
    drawer.classList.remove('-translate-x-full');
}

function closeDrawer() {
    const drawer = document.getElementById('drawer-form');
    drawer.classList.add('-translate-x-full');
}

// Inicijalizacija datepickera
document.addEventListener('DOMContentLoaded', function () {
        const datepickerEl = document.querySelector('#date');

        if (datepickerEl) {
            const datepicker = new Datepicker(datepickerEl, {
                autohide: true,
                clearBtn: true,
                format: 'mm/dd/yyyy'  // Postavi format na mm/dd/yyyy
            });

            // Dodaj event listener za klik na "Today" dugme
            datepickerEl.addEventListener('focus', function () {
                setTimeout(function () {
                    const todayButton = document.querySelector('.today-btn');
                    
                    if (todayButton) {
                        todayButton.addEventListener('click', function () {
                            const today = new Date();
                            const formattedDate = ('0' + (today.getMonth() + 1)).slice(-2) + '/' + 
                                                  ('0' + today.getDate()).slice(-2) + '/' + 
                                                  today.getFullYear();
                            datepickerEl.value = formattedDate;
                            datepicker.update(formattedDate);  // Ručno ažuriraj datepicker vrijednost
                        });
                    }
                }, 100);
            });
        }
    });

    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('i');
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            input.type = "password";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('#drawer-form form');

        form.addEventListener('submit', function (e) {
            const dateInput = document.querySelector('#date').value;
            const startTimeInput = document.querySelector('#start-time').value;
            
            if (dateInput && startTimeInput) {
                const dateTimeField = document.createElement('input');
                dateTimeField.type = 'hidden';
                dateTimeField.name = 'date_time';
                dateTimeField.value = `${dateInput} ${startTimeInput}`;
                form.appendChild(dateTimeField);
            }
        });
    });

  
    document.addEventListener('DOMContentLoaded', function () {
    const zavrseniTermini = document.querySelectorAll('[data-status="zavrsen"]');

    zavrseniTermini.forEach(function (termin) {
        termin.style.transition = 'opacity 1s';
        termin.style.opacity = '0';
        setTimeout(() => {
            termin.remove();
        }, 1000);
    });
});


// FILTRIRANJE LOKACIJA PO GRADOVIMA
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
                console.error('Error fetching locations:', error);
                locationSelect.innerHTML = '<option value="" disabled>Greška pri učitavanju</option>';
            });
    }




    document.addEventListener('DOMContentLoaded', function () {
    @foreach ($needsReview as $room)
        document.getElementById('reviewModal-{{ $room->id }}').classList.remove('hidden');
    @endforeach
});


function closeReviewModal(roomId) {
    const modal = document.getElementById(`reviewModal-${roomId}`);
    modal.classList.add('hidden'); // Sakrij modal
}


</script>
@endsection
