<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  
<!-- Učitavanje Tailwind iz Vite build-a -->
@vite('resources/css/app.css')

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HajmoTermin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="{{ asset('build/assets/app-w2Ig1LG7.css') }}">
    <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Inter:wght@300;500&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.9.6/dist/cdn.min.js" defer></script>



</head>



<body class="bg-gray-100 font-inter">

<nav class="bg-gray-900 border-gray-200 dark:bg-gray-900 fixed top-0 left-0 w-full shadow-md z-50 font-inter">
  <div class="container mx-auto px-4 py-4 flex justify-between items-center">
    <!-- Logo -->
    <a href="{{ route('home') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
      <img src="https://flowbite.com/docs/images/logo.svg" class="h-8" alt="HajmoTermin Logo" />
      <span class="text-2xl font-semibold text-white">Terminator</span>
    </a>

    <!-- Navigacija za desktop -->
<div class="hidden md:flex items-center space-x-6">
  <!-- Dropdown za Termini -->
  <div class="relative">
    <button type="button" class="text-white hover:text-blue-500 transition duration-200 flex items-center" id="termini-menu-button" aria-expanded="false" data-dropdown-toggle="termini-dropdown">
      Termini
      <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
      </svg>
    </button>

    <!-- Dropdown meni za Termini -->
    <div class="hidden absolute right-0 mt-2 w-48 dark:bg-gray-800 rounded-lg shadow-lg z-50" id="termini-dropdown">
      <ul class="py-2">
        <li>
          <a href="{{ route('rooms.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Pregled termina</a>
        </li>
        <div class="border-t border-gray-600 my-1"></div>
        <li>
          <a href="{{ route('rooms.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Dodaj novi termin</a>
        </li>
      </ul>
    </div>
  </div>

  <a href="{{ route('dashboard') }}" class="text-white hover:text-blue-500 transition duration-200">Dashboard</a>
</div>



<!-- Notifikacije i profil -->
<div class="flex items-center space-x-4">
    <!-- Notifikacije -->
    <div class="relative">
        @auth
        <button id="notification-bell" class="text-white relative">
            <i class="fas fa-bell text-2xl"></i>
            @if(auth()->user()->unreadNotifications->count() > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                {{ auth()->user()->unreadNotifications->count() }}
            </span>
            @endif
        </button>
        @endauth

        <!-- Padajući meni notifikacija -->
<div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-[450px] bg-white rounded-xl shadow-2xl z-50 dark:bg-gray-800">
    <div class="px-6 py-4 border-b dark:border-gray-700 flex justify-between items-center">
        <p class="text-lg font-semibold text-gray-900 dark:text-white">Obavijesti</p>
        <!-- <button class="text-sm text-blue-500 hover:underline">Očisti sve</button> -->
    </div>

    <!-- Tabovi za filtriranje -->
<div class="flex px-6 pt-4 space-x-8 text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
    <button id="tab-all" data-tab="all" class="text-blue-500 font-semibold border-b-2 border-blue-500 pb-2">Sve</button>
    <button id="tab-profile" data-tab="profile" class="hover:text-blue-500">Profil</button>
    <button id="tab-feedback" data-tab="feedback" class="hover:text-blue-500">Feedback</button>
</div>

<!-- Lista notifikacija -->
<div class="max-h-96 overflow-y-auto custom-scrollbar">

    @auth
        <!-- Sve notifikacije (po defaultu prikazane) -->
        <div data-tab-content="all">
            @forelse(auth()->user()->unreadNotifications as $notification)
                <div class="px-6 py-4 border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200">
                    <div class="flex items-start space-x-6">
                        <div class="relative">
                            <img class="w-14 h-14 rounded-full border-2 border-gray-300 shadow-lg"
                                 src="{{ $notification->data['inviter_photo'] ? asset('storage/' . $notification->data['inviter_photo']) : 'https://ui-avatars.com/api/?name=' . urlencode($notification->data['inviter_name']) }}"
                                 alt="User Photo">
                        </div>
                        <div class="flex-1">
                            <p class="text-md font-semibold text-gray-900 dark:text-white">
                                {{ $notification->data['inviter_name'] ?? 'Nepoznat korisnik' }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $notification->data['message'] }}
                            </p>
                            <div class="mt-4 flex space-x-4">
                                <form method="POST" action="{{ route('invite.accept', ['notification' => $notification->id]) }}">
                                    @csrf
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow-lg">Prihvati</button>
                                </form>
                                <form method="POST" action="{{ route('invite.decline', ['notification' => $notification->id]) }}">
                                    @csrf
                                    <button type="submit" class="bg-gray-400 hover:bg-gray-500 text-black px-5 py-2 rounded-lg">Odbij</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Nemate novih obavijesti.</p>
                </div>
            @endforelse
        </div>

        <!-- Profil sekcija (skrivena po defaultu) -->
        <div data-tab-content="profile" class="hidden">
            <div class="p-6 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">Nemate novih obavijesti za Profil.</p>
            </div>
        </div>

        <!-- Feedback sekcija (skrivena po defaultu) -->
        <div data-tab-content="feedback" class="hidden">
            <div class="p-6 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">Nema novih povratnih informacija.</p>
            </div>
        </div>

    @else
        <!-- Ako korisnik nije prijavljen -->
        <div class="p-6 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Morate biti prijavljeni da biste vidjeli obavijesti.</p>
        </div>
    @endauth

</div>


   
    <!-- Profil sekcija (skrivena po defaultu) -->
    <div data-tab-content="feedback" class="hidden">
        <div class="p-6 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Nema novih povratnih informacija.</p>
        </div>
    </div>





    <!-- Feedback sekcija (skrivena po defaultu) -->
    <div data-tab-content="feedback" class="hidden">
        <div class="p-6 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Nema novih povratnih informacija.</p>
        </div>
    </div>
  </div>
</div>




 
      @auth
      <!-- Profil meni -->
      <div class="relative">
    <button type="button" class="flex items-center text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown">
        <span class="sr-only">Open user menu</span>
        @if(auth()->user()->profile_photo_path)
        <img class="w-8 h-8 rounded-full" 
     src="{{ auth()->user()->profile_photo_path ? asset('storage/' . auth()->user()->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}" 
     alt="User Photo">

        @else
            <img class="w-8 h-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}" alt="User Photo">
        @endif
    </button>

        <!-- Dropdown meni -->
        <div class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-50 dark:bg-gray-800" id="user-dropdown">
          <div class="px-4 py-3 border-b border-gray-300 dark:border-gray-600">
            <span class="block text-sm text-gray-900 dark:text-white">{{ auth()->user()->name }}</span>
            <span class="block text-sm text-gray-500 truncate dark:text-gray-400">{{ auth()->user()->email }}</span>
          </div>
          <ul class="py-2">
            <li>
              <a href="{{ route('players.profile', auth()->id()) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Profil</a>
            </li>
            <li>
              <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Dashboard</a>
            </li>
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Odjavi se</button>
              </form>
            </li>
          </ul>
        </div>
      </div>
      @else
      <ul class="hidden md:flex space-x-6">
        <li>
          <a href="{{ route('login') }}" class="text-white hover:text-blue-500 transition duration-200">Prijava</a>
        </li>
        <li>
          <a href="{{ route('register') }}" class="text-white hover:text-blue-500 transition duration-200">Registracija</a>
        </li>
      </ul>
      @endauth
      

      <!-- Mobile meni dugme -->
      <button data-collapse-toggle="navbar-user" type="button" class="md:hidden p-2 text-white rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
        <span class="sr-only">Open main menu</span>
        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

  <!-- Mobile meni -->
<div id="navbar-user" class="hidden md:hidden absolute top-16 right-4 w-48 bg-gray-800 text-white rounded-lg shadow-lg z-50">
    <ul class="space-y-4 mt-4">
        <li>
            <a href="{{ route('rooms.index') }}" class="block px-4 py-2 hover:bg-gray-700">Termini</a>
        </li>
        @auth
        <li>
            <a href="{{ route('rooms.create') }}" class="block px-4 py-2 hover:bg-gray-700">Dodaj novi termin</a>
        </li>
       
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full px-4 py-2 text-left hover:bg-gray-700">Odjavi se</button>
            </form>
        </li>
        @else
        <li>
            <a href="{{ route('login') }}" class="block px-4 py-2 hover:bg-gray-700">Prijava</a>
        </li>
        <li>
            <a href="{{ route('register') }}" class="block px-4 py-2 hover:bg-gray-700">Registracija</a>
        </li>
        @endauth
    </ul>
</div>
    </div>
  </div>
</nav>

<script>
 document.addEventListener('DOMContentLoaded', function () {
    const dropdownToggles = document.querySelectorAll('[data-dropdown-toggle]');
    const collapseToggles = document.querySelectorAll('[data-collapse-toggle]');

    // Dropdown meniji (Korisnik i Termini)
    dropdownToggles.forEach(toggle => {
        const targetDropdown = document.querySelector(`#${toggle.getAttribute('data-dropdown-toggle')}`);
        
        toggle.addEventListener('click', function () {
            targetDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', function (e) {
            if (!toggle.contains(e.target) && !targetDropdown.contains(e.target)) {
                targetDropdown.classList.add('hidden');
            }
        });
    });

    // Mobile meni (Hamburger)
    collapseToggles.forEach(toggle => {
        const targetMenu = document.querySelector(`#${toggle.getAttribute('data-collapse-toggle')}`);
        
        toggle.addEventListener('click', function () {
            targetMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', function (e) {
            if (!toggle.contains(e.target) && !targetMenu.contains(e.target)) {
                targetMenu.classList.add('hidden');
            }
        });
    });
});




    // Funkcija za promjenu teme i pohranjivanje u localStorage
    function toggleTheme() {
        if (localStorage.getItem('theme') === 'dark') {
            localStorage.setItem('theme', 'light');
            document.documentElement.classList.remove('dark');
        } else {
            localStorage.setItem('theme', 'dark');
            document.documentElement.classList.add('dark');
        }
    }

    // Automatski postavi temu iz localStorage-a
    if (localStorage.getItem('theme') === 'dark') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }


    // ZA OTVARANJE NOTIFIKACIJA

    document.addEventListener('DOMContentLoaded', function () {
    const notificationBell = document.getElementById('notification-bell');
    const notificationDropdown = document.getElementById('notification-dropdown');

    notificationBell.addEventListener('click', function () {
        notificationDropdown.classList.toggle('hidden');
    });

    // Zatvaranje dropdown-a kada klikneš van njega
    document.addEventListener('click', function (e) {
        if (!notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
            notificationDropdown.classList.add('hidden');
        }
    });
});





// ZA SWITCHANJE NOTIFIKACIJA TABOVI - SVE - PROFIL - FEEDBACK

document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('[id^="tab-"]');
    const allTab = document.querySelector('#tab-all');
    const profileTab = document.querySelector('#tab-profile');
    const feedbackTab = document.querySelector('#tab-feedback');

    const allContent = document.querySelector('[data-tab-content="all"]');
    const profileContent = document.querySelector('[data-tab-content="profile"]');
    const feedbackContent = document.querySelector('[data-tab-content="feedback"]');

    // Postavi default selekciju na "Sve" i prikaži sve notifikacije
    allTab.classList.add('text-blue-500', 'font-semibold', 'border-b-2', 'border-blue-500');
    allContent.classList.remove('hidden');

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            const target = this.getAttribute('data-tab');

            // Ukloni aktivne klase sa svih tabova
            tabs.forEach(t => {
                t.classList.remove('text-blue-500', 'font-semibold', 'border-b-2', 'border-blue-500');
            });

            // Dodaj aktivne klase samo na kliknuti tab
            this.classList.add('text-blue-500', 'font-semibold', 'border-b-2', 'border-blue-500');

            // Sakrij sve sadržaje
            document.querySelectorAll('[data-tab-content]').forEach(content => {
                content.classList.add('hidden');
            });

            // Prikaži odgovarajući sadržaj na osnovu klika
            if (target === 'all') {
                allContent.classList.remove('hidden');
            } else if (target === 'profile') {
                profileContent.classList.remove('hidden');
            } else if (target === 'feedback') {
                feedbackContent.classList.remove('hidden');
            }
        });
    });
});




</script>

    <!-- Glavni sadržaj -->
    <main class="container mx-auto px-4 py-8">
        @yield('content')
    </main>

   

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-4">
        <div class="container mx-auto text-center">
            <p>&copy; {{ date('Y') }} Terminator. Sva prava zadržana.</p>
        </div>
    </footer>
</body>

</html>
