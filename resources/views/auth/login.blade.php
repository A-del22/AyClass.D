<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login &mdash; {{ $appset->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-100 to-blue-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="mx-auto h-16 w-16 mb-6">
                    <img src="{{ asset('logo.png') }}" alt="Logo" class="h-full w-full object-cover rounded-full">
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang</h2>
                <p class="text-gray-600">Silakan masuk ke akun admin Anda</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-8 space-y-6">
                {{-- @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Terjadi kesalahan:</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif --}}

                @if (session('status'))
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ session('status') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <form class="space-y-6" action="{{ route('login') }}" method="POST" id="loginForm">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input id="email" name="email" type="text" required autocomplete="email"
                                value="{{ old('email') }}"
                                class="block w-full pl-10 pr-3 py-3 border @error('email') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 ease-in-out @error('email') focus:ring-red-500 @endif"
                                placeholder="Masukkan email address">
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="password"
                                class="block text-sm font-medium text-gray-700 mb-2">
                            Kata Sandi
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input id="password" name="password" type="password" required
                                    autocomplete="current-password"
                                    class="block w-full pl-10 pr-12 py-3 border @error('password') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 ease-in-out @error('password') focus:ring-red-500 @endif"
                                placeholder="Masukkan kata sandi">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button type="button" class="text-gray-400 hover:text-gray-600 toggle-password"
                                    onclick="togglePassword()">
                                    <i class="fas fa-eye" id="toggle-icon"></i>
                                </button>
                            </div>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex
                                    items-center justify-between">
                                <div class="flex items-center">
                                    <input id="remember" name="remember" type="checkbox"
                                        {{ old('remember') ? 'checked' : '' }}
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                                        Ingat saya
                                    </label>
                                </div>
                            </div>

                            <div>
                                <button type="submit" id="submitBtn"
                                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 ease-in-out transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                    <span class="absolute left-0 inset-y-0 flex items-center pl-3" id="submitIcon">
                                        <i class="fas fa-sign-in-alt text-blue-200 group-hover:text-blue-100"></i>
                                    </span>
                                    <span id="submitText">Masuk ke Dashboard</span>
                                </button>
                            </div>
                </form>
            </div>

            <div class="text-center">
                <p class="text-xs text-gray-500">
                    © {{ date('Y') }} {{ $appset->name }}. Semua hak dilindungi.
                </p>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggle-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const submitIcon = document.getElementById('submitIcon');
            const submitText = document.getElementById('submitText');

            submitBtn.disabled = true;
            submitIcon.innerHTML = '<i class="fas fa-spinner fa-spin text-blue-200"></i>';
            submitText.textContent = 'Sedang masuk...';
        });

        function setupInputValidation() {
            const inputs = document.querySelectorAll('input[required]');

            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    const parent = this.closest('.relative') || this.parentElement;
                    if (!this.classList.contains('border-red-300')) {
                        parent.classList.add('ring-2', 'ring-blue-500');
                    }
                });

                input.addEventListener('blur', function() {
                    const parent = this.closest('.relative') || this.parentElement;
                    parent.classList.remove('ring-2', 'ring-blue-500');
                });

                input.addEventListener('input', function() {
                    if (this.value.trim() !== '') {
                        this.classList.remove('border-red-300');
                        this.classList.add('border-green-300');
                    }
                });
            });
        }

        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email) || email.length >= 3;
        }

        function setupClientValidation() {
            const form = document.getElementById('loginForm');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');

            form.addEventListener('submit', function(e) {
                let isValid = true;

                emailInput.classList.remove('border-red-300');
                passwordInput.classList.remove('border-red-300');

                if (!emailInput.value.trim()) {
                    emailInput.classList.add('border-red-300');
                    isValid = false;
                } else if (!validateEmail(emailInput.value.trim())) {
                    emailInput.classList.add('border-red-300');
                    isValid = false;
                }

                if (!passwordInput.value.trim()) {
                    passwordInput.classList.add('border-red-300');
                    isValid = false;
                } else if (passwordInput.value.length < 3) {
                    passwordInput.classList.add('border-red-300');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();

                    const submitBtn = document.getElementById('submitBtn');
                    const submitIcon = document.getElementById('submitIcon');
                    const submitText = document.getElementById('submitText');

                    submitBtn.disabled = false;
                    submitIcon.innerHTML =
                        '<i class="fas fa-sign-in-alt text-blue-200 group-hover:text-blue-100"></i>';
                    submitText.textContent = 'Masuk ke Dashboard';

                    showNotification('Mohon periksa kembali form yang Anda isi', 'error');
                }
            });
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className =
                `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;

            if (type === 'error') {
                notification.className += ' bg-red-100 border border-red-400 text-red-700';
                notification.innerHTML = `<i class="fas fa-exclamation-circle mr-2"></i>${message}`;
            } else {
                notification.className += ' bg-blue-100 border border-blue-400 text-blue-700';
                notification.innerHTML = `<i class="fas fa-info-circle mr-2"></i>${message}`;
            }

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);

            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 5000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            setupInputValidation();
            setupClientValidation();

            const alerts = document.querySelectorAll('.bg-red-50, .bg-green-50');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease-out';
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.parentNode.removeChild(alert);
                        }
                    }, 500);
                }, 5000);
            });

            document.getElementById('email').focus();
        });

        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                const submitBtn = document.getElementById('submitBtn');
                const submitIcon = document.getElementById('submitIcon');
                const submitText = document.getElementById('submitText');

                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitIcon.innerHTML =
                        '<i class="fas fa-sign-in-alt text-blue-200 group-hover:text-blue-100"></i>';
                    submitText.textContent = 'Masuk ke Dashboard';
                }
            }
        });
    </script>
</body>

</html>
