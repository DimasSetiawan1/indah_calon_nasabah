<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | SPK Kelayakan Nasabah</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 100%;
            max-width: 450px;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08), 0 5px 15px rgba(0, 0, 0, 0.03);
            background: white;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .brand-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
        }

        .brand-logo img {
            max-width: 140px;
            height: auto;
        }

        .form-control {
            border-radius: 10px;
            padding: 14px 18px;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            font-size: 0.95rem;
        }

        .form-control:focus {
            background-color: #ffffff;
            border-color: #0b5ed7;
            box-shadow: 0 0 0 0.25rem rgba(11, 94, 215, 0.15);
        }

        .btn-primary {
            padding: 14px;
            border-radius: 10px;
            font-weight: 600;
            background-color: #0b5ed7;
            border: none;
            transition: all 0.2s;
            font-size: 1rem;
            margin-top: 10px;
        }

        .btn-primary:hover {
            background-color: #0a4ebd;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(11, 94, 215, 0.2);
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="text-center mb-5">
            <div class="brand-logo">
                <img src="{{ asset('assets/logo.png') }}" alt="FIFGROUP Logo">
            </div>
            <h3 class="fw-bold text-dark mb-2">SPK Kelayakan Nasabah</h3>
            <p class="text-muted">Masuk untuk mengelola penilaian kelayakan</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-3 d-flex align-items-center p-3 mb-4">
                <i class="bi bi-exclamation-octagon-fill text-danger fs-5 me-2"></i>
                <span class="small">{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="form-label fw-semibold text-dark mb-2">Nama Pengguna</label>
                <input type="text" name="username" class="form-control" placeholder="username"
                    value="{{ old('username') }}" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold text-dark mb-2">Kata Sandi</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 shadow-sm">Masuk</button>
        </form>

        <div class="text-center mt-5 text-muted small">
            &copy; {{ date('Y') }} Sistem Penilaian Kelayakan Nasabah FIFGROUP
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>