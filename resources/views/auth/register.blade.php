@extends('layouts.app')

@section('content')
<style>
    html, body {
        margin: 0;
        padding: 0;
        height: 100%;
        overflow: hidden; /* Onemogućava skrol */
    }
</style>

<div class="flex h-screen m-0 p-0 overflow-hidden">
    <!-- Lijeva strana: slika zauzima cijelu polovinu ekrana na većim ekranima -->
    <div class="absolute left-0 top-0 w-1/2 h-full bg-cover bg-no-repeat hidden md:block">
        <img src="https://images.unsplash.com/photo-1680537732310-4cb363ddfcf4?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" 
             alt="Background Image"
             class="w-full h-full object-cover">
             
    </div>




    <!-- Desna strana: register forma, zauzima cijeli ekran na mobilnim uređajima -->
    <div class="relative w-full md:w-1/3 ml-auto h-screen flex items-center justify-center">
        <div class="w-full max-w-md p-8">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Registrujte se na Terminator</h2>

            <!-- Register Forma -->
            <form method="POST" action="{{ route('register') }}" onsubmit="return validatePasswords()">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Ime</label>
                    <input type="text" name="name" id="name" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div class="mb-4 relative">
                    <label for="password" class="block text-sm font-medium text-gray-700">Lozinka</label>
                    <div class="relative flex items-center">
                        <input type="password" name="password" id="password" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <button type="button" onclick="togglePassword('password', this)" class="absolute inset-y-0 right-3 flex items-center text-gray-500 focus:outline-none">
                            <i class="fas fa-eye-slash"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4 relative">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Potvrdi lozinku</label>
                    <div class="relative flex items-center">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute inset-y-0 right-3 flex items-center text-gray-500 focus:outline-none">
                            <i class="fas fa-eye-slash"></i>
                        </button>
                    </div>
                </div>

                <div class="flex justify-between items-center mb-6">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="terms" class="text-blue-500" id="terms" required>
                        <span class="ml-2 text-sm text-gray-700">Prihvatam uslove korištenja</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg shadow">
                    Registruj se
                </button>
            </form>

            <p class="mt-6 text-center text-gray-600 text-sm">
                Već imate nalog? <a href="{{ route('login') }}" class="text-blue-500 hover:text-blue-600 font-semibold">Prijavite se</a>
            </p>
        </div>
    </div>
</div>

<script>
    function validatePasswords() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;

        if (password !== confirmPassword) {
            alert('Lozinke se ne podudaraju. Molimo pokušajte ponovo.');
            return false;
        }
        return true;
    }

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
</script>
@endsection
