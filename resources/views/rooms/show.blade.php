@extends('layouts.app')

@section('content')
<div class="h-screen bg-gradient-to-b from-gray-100 to-gray-200 flex flex-col pt-16 font-inter">
<!-- Toaster Container -->
<div id="toast-container" class="fixed bottom-5 right-5 z-50 space-y-4"></div>
    <!-- Header -->
    <header class="bg-white shadow-lg p-4 flex items-center justify-between">
        <div>
            <a href="{{ route('rooms.index') }}" class="text-blue-600 hover:text-blue-700 font-medium flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Termini
            </a>
        </div>
        <h1 class="text-lg md:text-xl font-bold text-gray-800">Detalji termina</h1>
    </header>

    <!-- Main Content -->
    <div class="flex flex-1 flex-col md:flex-row overflow-hidden font-inter">
<!-- Left Sidebar - Lista igrača -->
<aside class="w-full md:w-1/4 bg-gray-800 text-white p-4 md:p-6 overflow-y-auto shadow-inner">
    <h2 class="text-lg md:text-xl font-semibold mb-4 border-b border-gray-600 pb-2">Pridruženi igrači</h2>
    <ul class="space-y-4">
        @forelse ($players as $player)
            <li id="player-{{ $player->id }}" class="group flex items-center justify-between hover:bg-gray-700 p-3 rounded-lg transition duration-200 ease-in-out">
                <div class="flex items-center">
                    <div class="w-8 h-8 md:w-10 md:h-10 flex items-center justify-center bg-blue-500 rounded-full text-white font-bold shadow-md">
                        {{ substr($player->name, 0, 1) }}
                    </div>
                    <span class="ml-4 text-sm md:text-lg">{{ $player->name }}</span>
                </div>

                <!-- Dugmad na hover -->
                <div class="flex items-center space-x-2 md:space-x-4 opacity-0 group-hover:opacity-100 transition duration-200 ease-in-out">
                    <!-- Link za profil igrača (vidljivo svima na hover) -->
                    <a href="{{ route('players.profile', $player->id) }}" 
                       title="Pogledaj profil igrača"
                       class="text-blue-500 hover:text-white hover:bg-blue-500 rounded-full p-2">
                        <i class="fas fa-user-circle"></i>
                    </a>

                    <!-- Dugme za izbacivanje igrača (samo vlasnik sobe) -->
                    @if (auth()->id() === $room->owner_id)
                        <button 
                            onclick="kickPlayer({{ $player->id }})" 
                            title="Izbaci igrača"
                            class="text-red-500 hover:text-white hover:bg-red-500 rounded-full p-2">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                </div>
            </li>
        @empty
            <p class="text-gray-400">Nema pridruženih igrača.</p>
        @endforelse
    </ul>
</aside>


        <!-- Middle Section - Informacije o terminu -->
        <main class="flex-1 bg-white p-4 md:p-8 flex flex-col justify-between shadow-lg">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">Informacije o terminu</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                <div class="p-4 md:p-6 bg-gray-100 rounded-lg shadow-md hover:shadow-lg transition duration-200">
    <p><strong>Lokacija:</strong> {{ $room->location->name }}</p>
    <p><strong>Datum:</strong> {{ \Carbon\Carbon::parse($room->date)->format('d.m.Y') }}</p>
    <p><strong>Vrijeme početka:</strong> {{ \Carbon\Carbon::parse($room->start_time)->format('H:i') }}</p>
</div>

                    <div class="p-4 md:p-6 bg-gray-100 rounded-lg shadow-md hover:shadow-lg transition duration-200">
                        <p><strong>Kreirao:</strong> {{ $room->owner ? $room->owner->name : 'Nepoznato' }}</p>
                        <p><strong>Broj igrača:</strong> {{ count($players) }}/{{ $room->max_players }}</p>
                    </div>
                </div>
                <div class="mt-4 p-4 md:p-6 bg-gray-100 rounded-lg shadow-md hover:shadow-lg transition duration-200">
                    <p><strong>Opis:</strong> {{ $room->description ?? 'Nema opisa' }}</p>
                </div>
            </div>



<!-- Buttons -->
<div class="mt-4 md:mt-8">
    @if (auth()->id() === $room->owner_id)
        <div class="flex justify-between items-center flex-wrap">
            
            <!-- Dugme za uređivanje termina -->
            <a href="{{ route('rooms.edit', $room->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded shadow-md transition duration-200 mr-1">
                Uredi termin
            </a>

            <!-- Forma za brisanje termina -->
            <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" onsubmit="return confirm('Da li ste sigurni da želite obrisati ovaj termin?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded shadow-md transition duration-200 mr-1">
                    Obriši termin
                </button>
            </form>

            <!-- Forma za završavanje termina -->
            <form action="{{ route('rooms.finish', $room->id) }}" method="POST" onsubmit="return confirm('Da li ste sigurni da želite završiti ovaj termin?')">
                @csrf
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded shadow-md transition duration-200 mr-1">
                    Završi termin
                </button>
            </form>

                   <!-- Forma za završavanje termina -->
                   <form action="{{ route('rooms.finishWithReviews', $room->id) }}" method="POST" onsubmit="return confirm('Da li ste sigurni da želite da pokrenete ocjenjivanje igrača?')">
    @csrf
    <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-2 rounded shadow-md transition duration-200 mr-1">
        Ocjenjivanje
    </button>
</form>

            <!-- Dugme za pozivanje igrača -->
            <button onclick="openModal()" class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded shadow-md transition duration-200">
                Pozovi igrača
            </button>
        </div>
    @endif






    <!-- Forma za napuštanje termina -->
    <form action="{{ route('rooms.leave', $room->id) }}" method="POST" class="mt-4">
        @csrf
        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded shadow-md w-full transition duration-200">
            Napusti termin
        </button>
    </form>
</div>

        </main>

        <!-- Right Sidebar - Chat -->
        <aside class="w-full md:w-1/4 bg-gray-50 p-4 md:p-6 flex flex-col shadow-inner">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-4">Chat</h2>
            <div id="chatBox" class="flex-1 bg-white p-4 rounded shadow-md overflow-y-auto">
                @foreach ($messages as $message)
                    <div class="p-3 border-b hover:bg-gray-100 rounded-lg">
                        <span class="font-semibold text-gray-800">{{ $message->user->name }}:</span>
                        <span class="text-gray-600">{{ $message->content }}</span>
                    </div>
                @endforeach
            </div>
            <form action="{{ route('chat.send', $room->id) }}" method="POST" class="mt-4">
                @csrf
                <div class="flex items-center space-x-2">
                    <input type="text" name="message" placeholder="Unesite poruku..." class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded shadow-md">
                        Pošalji
                    </button>
                </div>
            </form>
        </aside>
    </div>
</div>


<!-- Modal -->
<div id="invitePlayerModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden flex justify-center items-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-1/3 p-6">
        <h2 class="text-2xl font-semibold mb-4">Pozovi igrača</h2>

        <!-- Pretraga igrača -->
        <input type="text" id="playerSearch" class="border p-2 w-full mb-4 rounded" placeholder="Pretraži igrača..." onkeyup="searchPlayers()">

        <!-- Lista igrača (dinamički se puni) -->
        <div class="max-h-60 overflow-y-auto" id="playersList">
            <!-- Igrači će biti prikazani ovdje preko AJAX-a -->
        </div>

        <!-- Dugme za zatvaranje modala -->
        <div class="flex justify-end mt-4">
            <button onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Zatvori
            </button>
        </div>
    </div>
</div>








<script>

function kickPlayer(playerId) {
        if (confirm("Jeste li sigurni da želite izbaciti ovog igrača?")) {
            fetch(`/rooms/{{ $room->id }}/kick/${playerId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
            })
            .then(response => {
                if (response.ok) {
                    document.getElementById(`player-${playerId}`).remove();
                    showToast("Igrač je uspješno izbačen.", 'success');
                } else {
                    response.json().then(data => {
                        showToast(data.error || "Greška prilikom izbacivanja igrača.", 'error');
                    });
                }
            })
            .catch(error => {
                showToast("Došlo je do greške. Pokušajte ponovo.", 'error');
            });
        }
    }

// Za notifikacije

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


    // LOGIKA ZA POZIVANJE IGRAČA NA TERMIN

    // Otvori modal
    function openModal() {
    const modal = document.getElementById('invitePlayerModal');
    modal.classList.remove('hidden');
}

// Zatvaranje modala
function closeModal() {
    const modal = document.getElementById('invitePlayerModal');
    modal.classList.add('hidden');
}


 // AJAX pretraga igrača
 function searchPlayers() {
    let query = document.getElementById('playerSearch').value;
    let roomId = '{{ $room->id }}';

    fetch(`/search-players?query=${query}&room_id=${roomId}`)
        .then(response => response.json())
        .then(data => {
            let playersList = document.getElementById('playersList');
            playersList.innerHTML = '';

            if (data.length > 0) {
                data.forEach(player => {
                    playersList.innerHTML += `
                        <div class="flex justify-between items-center p-2 border-b">
                            <span class="text-lg">${player.name}</span>
                            <form method="POST" action="{{ route('invite.player') }}">
                                @csrf
                                <input type="hidden" name="user_id" value="${player.id}">
                                <input type="hidden" name="room_id" value="{{ $room->id }}">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                    Pošalji zahtjev
                                </button>
                            </form>
                        </div>
                    `;
                });
            } else {
                playersList.innerHTML = '<div class="text-center p-2">Nema rezultata.</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}




</script>
@endsection
