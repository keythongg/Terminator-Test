@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden p-8">
        <h2 class="text-3xl font-semibold mb-6">Uredi Profil</h2>

        <div class="text-center mb-8">
    <div class="relative inline-block">
        <div class="w-48 h-48 rounded-full overflow-hidden border-4 border-blue-500 shadow-xl transform hover:scale-105 transition duration-300 ease-in-out">
          <!-- Prikaz slike profila -->
<img id="profileImage" class="w-full h-full object-cover" 
     src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : asset('/images/default-profile.png') }}" 
     alt="Profile Photo">

<!-- Label za izmjenu slike (samo ako je korisnik vlasnik profila) -->
@if (auth()->id() == $user->id)
<label for="profile_photo" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 rounded-full opacity-0 hover:opacity-100 transition duration-300 cursor-pointer">
    <div class="flex items-center space-x-2 text-white">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
            <path d="M17.414 2.586a2 2 0 00-2.828 0L9 8.172V10h1.828l5.586-5.586a2 2 0 000-2.828zM7 11v2h2l7-7-2-2-7 7zm6.586-9L15 5.414 13.586 7 12 5.414 13.586 3z" />
        </svg>
        <span class="font-medium">Izmijeni</span>
    </div>
</label>
<input type="file" name="profile_photo" id="profile_photo" class="hidden" accept="image/*">
@endif


            <!-- Input za odabir slike -->
            <input type="file" name="profile_photo" id="profile_photo" class="hidden" accept="image/*">
        </div>
    </div>
    <h2 class="mt-6 text-4xl font-bold text-gray-900">{{ $user->name }}</h2>
    <p class="text-lg text-gray-600">{{ $user->email }}</p>

    <p class="text-sm text-gray-400 mt-2 italic">Last seen: {{ $user->last_seen ? $user->last_seen->diffForHumans() : 'N/A' }}</p>
</div>



        <div class="border-b border-gray-200">
            <nav class="flex space-x-4">
                <button class="tab-link text-gray-600 py-4 px-6 hover:text-blue-500 focus:outline-none focus:text-blue-500" onclick="openTab(event, 'osnovni')">Osnovni podaci</button>
                <button class="tab-link text-gray-600 py-4 px-6 hover:text-blue-500 focus:outline-none focus:text-blue-500" onclick="openTab(event, 'historija')">Historija</button>
                <button class="tab-link text-gray-600 py-4 px-6 hover:text-blue-500 focus:outline-none focus:text-blue-500" onclick="openTab(event, 'ocjene')">Ocjene</button>
            </nav>
        </div>
        <div id="osnovni" class="tab-content mt-6 hidden">
    @if (auth()->id() == $user->id)
    <form method="POST" action="{{ route('profile.update.info') }}" class="space-y-6">
        @csrf
        <!-- Ime -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Ime</label>
            <input type="text" name="name" value="{{ $user->name }}" required 
                   class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" value="{{ $user->email }}" required 
                   class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>

        <!-- Broj Mobitela -->
        <div>
            <label for="broj_mobitela" class="block text-sm font-medium text-gray-700">Broj Mobitela</label>
            <input type="text" name="broj_mobitela" value="{{ $user->broj_mobitela }}"
                   class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>

      <!-- Nova Lozinka -->
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Nova Lozinka</label>
        <input type="password" name="password" placeholder="Unesite novu lozinku" 
               class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
    </div>

    <!-- Potvrda Lozinke -->
    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Potvrdi Lozinku</label>
        <input type="password" name="password_confirmation" placeholder="Potvrdite novu lozinku" 
               class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
    </div>

        <!-- Submit dugme -->
        <div class="flex justify-end">
            <button type="submit" 
                    class="inline-flex justify-center rounded-lg border border-transparent bg-blue-600 py-3 px-6 text-white font-medium shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Sačuvaj
            </button>
        </div>
    </form>
    @else
    <!-- Prikaz podataka (samo čitanje) -->
<div class="space-y-6">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Ime</label>
        <input type="text" id="name" value="{{ $user->name }}" 
               class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-gray-100 sm:text-sm"
               readonly>
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" id="email" value="{{ $user->email }}" 
               class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-gray-100 sm:text-sm"
               readonly>
    </div>

    <div>
        <label for="broj_mobitela" class="block text-sm font-medium text-gray-700">Broj Mobitela</label>
        <input type="text" id="broj_mobitela" value="{{ $user->broj_mobitela }}" 
               class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-gray-100 sm:text-sm"
               readonly>
    </div>
</div>
    @endif
</div>




        <div id="historija" class="tab-content mt-6 hidden">
        <h3 class="text-2xl font-semibold text-gray-800 mb-6">Historija soba</h3>
            <ul class="mt-4 space-y-4">
                @foreach($rooms as $room)
                    <li class="p-4 bg-gray-50 rounded-lg shadow-sm flex justify-between items-center">
                        <div>
                            <p class="font-semibold">{{ $room->name }}</p>
                            <p class="text-sm text-gray-500">{{ $room->pivot->created_at->setTimezone('Europe/Sarajevo')->format('d.m.Y H:i') }}</p>
                        </div>
                        <span class="text-sm bg-green-100 text-green-700 px-3 py-1 rounded-full">Pridružio se</span>
                    </li>
                @endforeach
            </ul>
        </div>
        <div id="ocjene" class="tab-content mt-8">
    <h2 class="text-3xl font-extrabold text-gray-800 mb-6">Ocjene</h2>

    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                @php
                    $averageRating = $receivedRatings->avg('rating');
                    $ratingCategory = '';

                    if ($averageRating >= 4.5) {
                        $ratingCategory = 'Izvrsno';
                        $badgeColor = 'bg-green-500';
                    } elseif ($averageRating >= 3.5) {
                        $ratingCategory = 'Vrlo dobro';
                        $badgeColor = 'bg-blue-500';
                    } elseif ($averageRating >= 2.5) {
                        $ratingCategory = 'Dobro';
                        $badgeColor = 'bg-yellow-500';
                    } else {
                        $ratingCategory = 'Loše';
                        $badgeColor = 'bg-red-500';
                    }
                @endphp

                <div class="flex items-center space-x-4">
                    <p class="text-lg font-bold">Prosječna ocjena:</p>
                    <div class="flex items-center space-x-2">
                        <span class="text-2xl font-extrabold text-gray-800">{{ number_format($averageRating, 1) }}</span>
                        <div class="flex">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 {{ $i <= $averageRating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 3.414a1 1 0 011.902 0l1.669 4.832a1 1 0 00.95.676h5.08c.969 0 1.371 1.24.588 1.81l-4.106 2.993a1 1 0 00-.364 1.118l1.557 4.703c.312.94-.797 1.71-1.637 1.118l-4.106-2.993a1 1 0 00-1.176 0l-4.106 2.993c-.84.592-1.949-.178-1.637-1.118l1.557-4.703a1 1 0 00-.364-1.118L2.762 10.732c-.783-.57-.381-1.81.588-1.81h5.08a1 1 0 00.95-.676l1.669-4.832z" />
                                </svg>
                            @endfor
                        </div>
                    </div>
                    <span class="text-sm text-white px-3 py-1 rounded-full {{ $badgeColor }}">{{ $ratingCategory }}</span>
                </div>
            </div>
        </div>
    </div>

    @if ($receivedRatings->isEmpty())
        <div class="text-center py-10 bg-white shadow-md rounded-lg">
            <p class="text-gray-500 text-lg italic">Nema primljenih ocjena.</p>
        </div>
    @else
        <div class="bg-white shadow-md rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                            Ocjenu dao
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                            Ocjena
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($receivedRatings as $rating)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-800 font-medium">
                                {{ $rating->rater->name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800">
                                {{ $rating->rating }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>




        </div>
    </div>
</div>
          
        </div>
    </div>
</div>

<script>
    function openTab(evt, tabName) {
        let i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.add("hidden");
        }
        tablinks = document.getElementsByClassName("tab-link");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("text-blue-500");
        }
        document.getElementById(tabName).classList.remove("hidden");
        evt.currentTarget.classList.add("text-blue-500");
    }

  /////////////////////////  JAVASCRIPT ZA UČITAVANJE PROFILNE SLIKE   ///////////////////////// 

    document.getElementById('profile_photo').addEventListener('change', function () {
    let formData = new FormData();
    formData.append('profile_photo', this.files[0]);

    fetch("{{ route('profile.update') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();  // Osvježava stranicu kako bi se nova slika učitala
        } else {
            alert('Greška prilikom učitavanja slike.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

document.getElementById('profile_photo').addEventListener('change', function () {
    let formData = new FormData();
    formData.append('profile_photo', this.files[0]);

    fetch("{{ route('profile.update') }}", {
    method: 'PATCH',  // Ispravi metod na PATCH
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
    },
    body: formData,
})

    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Automatski ažurira sliku profila na frontend-u
            document.getElementById('profileImage').src = data.image_path;
        } else {
            alert('Došlo je do greške prilikom učitavanja slike.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
  ///////////////////////// JAVASCRIPT ZA UČITAVANJE PROFILNE SLIKE   ///////////////////////// 

</script>
@endsection
