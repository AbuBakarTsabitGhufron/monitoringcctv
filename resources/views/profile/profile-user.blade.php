<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna - CCTV Lokasi DIY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .navbar {
            background: rgba(255,255,255,0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .profile-container {
            padding-top: 100px;
            padding-bottom: 50px;
        }
        
        .profile-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            border: 4px solid rgba(255,255,255,0.3);
        }
        
        .profile-avatar i {
            font-size: 60px;
            color: rgba(255,255,255,0.9);
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .btn-custom {
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, #3498db, #2980b9);
            border: none;
            color: white;
        }
        
        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #2980b9, #1abc9c);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }
        
        .btn-danger-custom {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            border: none;
            color: white;
        }
        
        .btn-danger-custom:hover {
            background: linear-gradient(135deg, #c0392b, #e67e22);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
        }
        
        .info-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid #3498db;
        }
        
        .alert-custom {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container">
            <a href="{{ auth()->user()->role === 'admin' ? route('lokasi.index') : route('home') }}" class="navbar-brand">
                <i class="fas fa-video text-primary me-2"></i>
                <strong>CCTV Lokasi DIY</strong>
            </a>
            <div class="ms-auto">
                <a href="{{ auth()->user()->role === 'admin' ? route('lokasi.index') : route('home') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container profile-container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="profile-card">
                    <!-- Profile Header -->
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h2 class="mb-1">{{ $user->name }}</h2>
                        <p class="mb-0 opacity-75">{{ $user->email }}</p>
                        <div class="mt-3">
                            <span class="badge bg-light text-dark px-3 py-2">
                                <i class="fas fa-shield-alt me-1"></i>
                                {{ $user->role === 'admin' ? 'Administrator' : 'Pengguna' }}
                            </span>
                        </div>
                    </div>

                    <!-- Profile Content -->
                    <div class="p-4">
                        <!-- Alert Messages -->
                        @if(session('success'))
                            <div class="alert alert-success alert-custom">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-custom">
                                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-custom">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- User Info -->
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                Informasi Akun
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Nama:</strong> {{ $user->name }}</p>
                                    <p><strong>Email:</strong> {{ $user->email }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Role:</strong> 
                                        <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : 'primary' }}">
                                            {{ $user->role === 'admin' ? 'Administrator' : 'Pengguna' }}
                                        </span>
                                    </p>
                                    <p><strong>Telepon:</strong> {{ $user->phone ?? 'Belum diisi' }}</p>
                                </div>
                            </div>
                            <p><strong>Bergabung sejak:</strong> {{ $user->created_at->format('d F Y') }}</p>
                            <p><strong>Terakhir diperbarui:</strong> {{ $user->updated_at->format('d F Y H:i') }}</p>
                        </div>

                        <!-- Edit Profile Form -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-edit me-2"></i>Edit Profil
                                </h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('profile-user.update') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">
                                                <i class="fas fa-user me-1"></i>Nama Lengkap
                                            </label>
                                            <input type="text" class="form-control" id="name" name="name" 
                                                   value="{{ old('name', $user->name) }}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">
                                                <i class="fas fa-envelope me-1"></i>Email
                                            </label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="{{ old('email', $user->email) }}" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">
                                            <i class="fas fa-phone me-1"></i>Nomor Telepon
                                        </label>
                                        <input type="text" class="form-control" id="phone" name="phone" 
                                               value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 08123456789">
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary-custom btn-custom">
                                            <i class="fas fa-save me-1"></i>Update Profil
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Change Password Form -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-key me-2"></i>Ubah Password
                                </h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('profile-user.change-password') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">
                                            <i class="fas fa-lock me-1"></i>Password Saat Ini
                                        </label>
                                        <input type="password" class="form-control" id="current_password" 
                                               name="current_password" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="new_password" class="form-label">
                                                <i class="fas fa-key me-1"></i>Password Baru
                                            </label>
                                            <input type="password" class="form-control" id="new_password" 
                                                   name="new_password" required minlength="8">
                                            <small class="text-muted">Minimal 8 karakter</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="new_password_confirmation" class="form-label">
                                                <i class="fas fa-key me-1"></i>Konfirmasi Password Baru
                                            </label>
                                            <input type="password" class="form-control" id="new_password_confirmation" 
                                                   name="new_password_confirmation" required minlength="8">
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-danger-custom btn-custom">
                                            <i class="fas fa-shield-alt me-1"></i>Ubah Password
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Form validation
        document.getElementById('new_password').addEventListener('input', function() {
            const password = this.value;
            const confirmation = document.getElementById('new_password_confirmation');
            
            if (password.length < 8) {
                this.setCustomValidity('Password harus minimal 8 karakter');
            } else {
                this.setCustomValidity('');
            }
        });

        document.getElementById('new_password_confirmation').addEventListener('input', function() {
            const password = document.getElementById('new_password').value;
            const confirmation = this.value;
            
            if (password !== confirmation) {
                this.setCustomValidity('Konfirmasi password tidak cocok');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>