<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | SPK FIF</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #0b5ed7;
            --primary-dark: #0a4ebd;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --body-bg: #f1f5f9;
            --card-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--body-bg);
            color: #334155;
        }
        /* Sidebar Styling */
        .sidebar {
            width: 280px;
            flex-shrink: 0;
            min-height: 100vh;
            background-color: var(--sidebar-bg);
            color: #e2e8f0;
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
            z-index: 1000;
        }
        .sidebar-brand {
            padding: 30px 25px;
            text-align: center;
            background-color: rgba(0,0,0,0.2);
            margin-bottom: 20px;
        }
        .sidebar-brand h5 {
            font-weight: 700;
            letter-spacing: 1px;
            margin: 0;
            color: #ffffff;
        }
        .sidebar-brand small {
            color: #94a3b8;
            font-size: 0.75rem;
        }
        .nav-item {
            margin: 8px 20px;
        }
        .nav-link {
            color: #cbd5e1;
            padding: 14px 20px;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        .nav-link i {
            font-size: 1.25rem;
            margin-right: 15px;
            width: 26px;
            text-align: center;
        }
        .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: white;
            transform: translateX(4px);
        }
        .nav-link.active {
            background-color: var(--primary-color);
            color: white;
            box-shadow: 0 4px 6px rgba(11, 94, 215, 0.25);
        }
        
        /* Main Content */
        .main-content {
            flex-grow: 1;
            min-width: 0;
            padding: 0;
            min-height: 100vh;
            background-color: var(--body-bg);
        }
        .top-navbar {
            background: white;
            padding: 20px 45px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }
        .page-title {
            margin: 0;
            font-weight: 700;
            color: #0f172a;
            font-size: 1.4rem;
        }
        .content-wrapper {
            padding: 0 45px 45px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        /* Components */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            margin-bottom: 24px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card-header {
            background-color: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 20px;
            font-weight: 600;
            color: #1e293b;
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
        }
        .btn {
            font-weight: 500;
            border-radius: 6px;
            padding: 8px 16px;
            transition: all 0.2s;
        }
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
        }
        .table {
            vertical-align: middle;
        }
        .table thead th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 16px;
        }
        .table tbody td {
            padding: 12px 16px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }
        .badge {
            font-weight: 500;
            padding: 6px 10px;
            border-radius: 6px;
        }
        .form-control, .form-select {
            border-radius: 6px;
            border-color: #cbd5e1;
            padding: 10px 15px;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(11, 94, 215, 0.15);
        }
    </style>
</head>
<body>

<div class="d-flex p-0">
    <!-- Sidebar -->
    <div class="sidebar d-none d-md-block">
            <div class="sidebar-brand">
                <h5>SPK NASABAH</h5>
                <small>BWM & TOPSIS SYSTEM</small>
            </div>
            
            <div class="nav flex-column">
                <div class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill"></i> Dashboard
                    </a>
                </div>
                
                <div class="px-3 mt-4 mb-2 text-uppercase text-white opacity-75" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 1px;">Master Data</div>
                <div class="nav-item">
                    <a href="{{ route('criterias.index') }}" class="nav-link {{ request()->routeIs('criterias.*') ? 'active' : '' }}">
                        <i class="bi bi-list-check"></i> Kriteria
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> Calon Nasabah
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('assessments.index') }}" class="nav-link {{ request()->routeIs('assessments.*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard2-data-fill"></i> Penilaian
                    </a>
                </div>
                
                <div class="px-3 mt-4 mb-2 text-uppercase text-white opacity-75" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 1px;">Perhitungan</div>
                <div class="nav-item">
                    <a href="{{ route('bwm.index') }}" class="nav-link {{ request()->routeIs('bwm.*') ? 'active' : '' }}">
                        <i class="bi bi-calculator-fill"></i> Analisis BWM
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('topsis.index') }}" class="nav-link {{ request()->routeIs('topsis.*') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart-line-fill"></i> Hasil TOPSIS
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Navbar -->
            <div class="top-navbar">
                <h4 class="page-title">@yield('title', 'Dashboard')</h4>
                
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center bg-white border rounded-pill px-3 py-2 shadow-sm" type="button" data-bs-toggle="dropdown">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; font-weight: bold; color: #ffffff !important;">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                        <span class="fw-semibold text-dark me-2">{{ Auth::user()->name ?? 'User' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 rounded-3">
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger fw-medium">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-wrapper">
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-check-circle-fill text-success fs-4 me-3"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-4 me-3"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
