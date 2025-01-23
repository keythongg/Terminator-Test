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

    <!-- Desna strana: login forma, zauzima cijeli ekran na mobilnim uređajima -->
    <div class="relative w-full md:w-1/3 ml-auto h-screen flex items-center justify-center font-inter">
        <div class="w-full max-w-md p-8">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-6 font-inter">Prijavi se na Terminator</h2>

            <!-- Login Forma -->
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 ">Email</label>
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

                <div class="flex justify-between items-center mb-6">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="remember" class="text-blue-500" id="remember">
                        <span class="ml-2 text-sm text-gray-700">Zapamti me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-blue-500 hover:text-blue-600 text-sm">Zaboravljena lozinka?</a>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg shadow">
                    Prijavi se
                </button>
            </form>

            <!-- Divider with lines -->
            <div class="relative flex py-6 items-center">
                <div class="flex-grow border-t border-gray-300"></div>
                <span class="flex-shrink mx-4 text-gray-500">Ili se prijavite putem</span>
                <div class="flex-grow border-t border-gray-300"></div>
            </div>

            <!-- Social Login Buttons -->
            <div class="flex justify-center mt-4 space-x-4">
            <a href="{{ url('auth/google') }}" 
   class="flex items-center justify-center w-full bg-gray-800 text-white font-semibold py-3 rounded-full shadow-lg hover:bg-gray-700 transition-all duration-200 ease-in-out transform hover:scale-105">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" viewBox="0 0 24 24" fill="currentColor">
        <path d="M23.998 12.274c0-.818-.067-1.602-.193-2.354H12v4.448h6.738c-.29 1.487-1.151 2.747-2.45 3.593v2.98h3.964c2.322-2.136 3.646-5.285 3.646-9.065z" fill="#FFFFFF"/>
        <path d="M12 24c3.24 0 5.96-1.08 7.947-2.937l-3.964-2.98c-1.1.733-2.507 1.168-3.983 1.168-3.065 0-5.664-2.071-6.596-4.854H1.373v3.057C3.423 21.498 7.423 24 12 24z" fill="#FFFFFF"/>
        <path d="M5.404 14.397A7.994 7.994 0 0 1 4.864 12c0-.832.144-1.635.396-2.397V6.545H1.373A12.001 12.001 0 0 0 0 12c0 1.943.472 3.777 1.373 5.455l4.031-3.058z" fill="#FFFFFF"/>
        <path d="M12 4.77c1.759 0 3.338.604 4.581 1.794l3.435-3.435C17.956 1.118 15.236 0 12 0 7.423 0 3.423 2.502 1.373 6.545l4.031 3.058C6.336 7.772 8.935 4.77 12 4.77z" fill="#FFFFFF"/>
    </svg>
    <span>Google</span>
</a>


              <!--   <a href="{{ url('auth/facebook') }}" class="flex items-center justify-center w-1/2 border border-gray-300 bg-white hover:bg-gray-100 text-gray-700 py-2 rounded-lg shadow">
                    <i class="fab fa-facebook text-gray-800 mr-2"></i>
                    <span>Facebook</span>
                </a> -->
            </div>

            <p class="mt-6 text-center text-gray-600 text-sm">
                Nemate račun? <a href="{{ route('register') }}" class="text-blue-500 hover:text-blue-600 font-semibold">Registrujte se</a>
            </p>
        </div>
    </div>
</div>

<script>
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
