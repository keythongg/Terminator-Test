<x-guest-layout>
    <!-- <x-slot name="logo">
        <a href="/">
            <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
        </a>
    </x-slot> -->

    <form method="POST" action="{{ route('profile.update.info') }}">
        @csrf

        <!-- Skriveno polje za ime (privremeno) -->
        <input type="hidden" name="name" value="{{ auth()->user()->name }}">

        <!-- Skriveno polje za email -->
        <input type="hidden" name="email" value="{{ auth()->user()->email }}">

        <!-- Lozinka -->
        <div class="mt-4">
            <label for="password" class="block font-medium text-sm text-gray-700">Nova šifra</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
        </div>

        <!-- Potvrda lozinke -->
        <div class="mt-4">
            <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Potvrdi šifru</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
        </div>

        <div class="flex items-center justify-end mt-4">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:outline-none focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                Sačuvaj šifru
            </button>
        </div>
    </form>
</x-guest-layout>
