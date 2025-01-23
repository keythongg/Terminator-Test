@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold mb-4">Unesite lozinku za pristup terminu</h2>
    <form action="{{ route('rooms.checkPassword', $room->id) }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="password" class="block text-gray-700 font-bold mb-2">Lozinka</label>
            <input type="password" id="password" name="password" class="border border-gray-300 rounded w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600">Pristupi</button>
    </form>
</div>
@endsection
