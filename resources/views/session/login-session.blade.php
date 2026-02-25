@extends('layouts.user_type.guest')

@section('content')
<main class="main-content mt-0" style="padding-top:0;margin-top:0;">
    <section class="vh-100 d-flex align-items-center justify-content-center" style="background-color: #007bff;">
        <div class="container d-flex justify-content-center">
            <div class="login-box d-flex flex-row bg-white shadow-lg" 
                style="width: 900px; border-radius: 40px; overflow: hidden;">

                <!-- Form Section -->
                <div class="form-section p-5 flex-fill d-flex flex-column justify-content-center" style="width: 50%;">
                    <h4 class="text-dark fw-bold mb-4 text-center">SISTEM MONITORING CCTV LOKASI</h4>
                    <!-- Gambar untuk mobile, tampil hanya di mobile -->
                    <div class="d-block d-md-none mb-4 text-center">
                        <img src="{{ asset('images/logocctv.jpg') }}" alt="CCTV" style="max-width: 200px; width: 100%; height: auto;">
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                style="width: 100%; border-radius: 30px;"
                                placeholder="Masukkan Email" value="{{ old('email') }}" required autofocus>
                        </div>

                        <div class="mb-3 position-relative">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                style="width: 100%; border-radius: 30px; padding-right: 40px;"
                                placeholder="Masukkan Password" required>
                            <button type="button" id="togglePassword" tabindex="-1" style="position: absolute; top: 38px; right: 16px; background: none; border: none; outline: none; padding: 0; height: 24px; width: 24px;">
                                <span id="eyeIcon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </span>
                            </button>
                        </div>

                        <div class="mb-3">
                            <button type="submit" 
                                style="background-color: #007bff; 
                                    color: white; 
                                    width: 100%; 
                                    border: none; 
                                    border-radius: 50px; 
                                    padding: 10px 0; 
                                    font-weight: bold;">
                                Sign In
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Image Section -->
                <div class="image-section d-none d-md-block flex-fill" style="
                    width: 50%;
                    background: url('{{ asset('images/logocctv.jpg') }}') no-repeat center center;
                    background-size: contain;
                    background-color: white;
                    min-height: 400px;">
                </div>
            </div>
        </div>
    </section>
</main>
@endsection

@push('scriptsku')
<style>
body.login-page {
    background: #007bff !important;
    margin: 0 !important;
    padding: 0 !important;
}
.main-content {
    margin-top: 0 !important;
    padding-top: 0 !important;
}
</style>
@endpush

@push('scriptsku')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');
        const eyeIcon = document.getElementById('eyeIcon');
        let show = false;
        if (togglePassword) {
            const eyeOpen = `<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`;
            const eyeClosed = `<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 012.223-3.592m3.104-2.727A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.965 9.965 0 01-4.293 5.411M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/></svg>`;
            // Set default: password hidden, show eyeClosed
            eyeIcon.innerHTML = eyeClosed;
            togglePassword.addEventListener('click', function() {
                show = !show;
                passwordInput.type = show ? 'text' : 'password';
                eyeIcon.innerHTML = show ? eyeOpen : eyeClosed;
            });
        }
    });
</script>
@endpush
