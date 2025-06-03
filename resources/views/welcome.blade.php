<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sispadu - Sistem Pengaduan Masyarakat</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Base styles */
        *,
        ::after,
        ::before {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --primary: #1a56db;
            --primary-dark: #1e429f;
            --secondary: #ef4444;
            --secondary-dark: #dc2626;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f9fafb;
            --bg-white: #ffffff;
            --android: #3ddc84;
            --ios: #007aff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* Container */
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Header */
        header {
            background-color: var(--bg-white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            height: 70px;
        }

        .logo {
            display: flex;
            align-items: center;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary);
            text-decoration: none;
        }

        .logo-icon {
            margin-right: 0.5rem;
            font-size: 1.8rem;
        }

        /* Navigation */
        .nav-menu {
            display: flex;
            align-items: center;
            /* Vertikal center */
            justify-content: center;
            /* Horizontal center */
            gap: 1rem;
            /* Spasi antar item (opsional) */
            list-style: none;
            /* Hilangkan bullet bawaan */
            padding: 0;
            margin: 0;
        }

        .nav-item {
            margin-left: 1.5rem;
        }

        .nav-link {
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: var(--primary);
        }

        .nav-link.active {
            color: var(--primary);
        }

        .btn {
            display: inline-block;
            padding: 0.6rem 1.5rem;
            border-radius: 0.375rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-secondary {
            background-color: var(--secondary);
            color: white;
        }

        .btn-secondary:hover {
            background-color: var(--secondary-dark);
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-dark);
        }

        /* Hero Section */
        .hero {
            padding-top: 120px;
            padding-bottom: 80px;
            background: linear-gradient(135deg, #f0f7ff 0%, #e6f0fd 100%);
        }

        .hero-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2rem;
        }

        .hero-content {
            flex: 1;
        }

        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-dark);
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 1.125rem;
            color: var(--text-light);
            margin-bottom: 2rem;
            max-width: 600px;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .hero-image {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .hero-image svg {
            max-width: 100%;
            height: auto;
        }

        /* App Download Section */
        .app-download {
            padding: 3rem 0;
            background-color: var(--bg-white);
            text-align: center;
        }

        .app-download-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .app-download-subtitle {
            font-size: 1.125rem;
            color: var(--text-light);
            margin-bottom: 2rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .app-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .app-button {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            color: white;
            font-weight: 500;
            transition: all 0.3s;
        }

        .app-button-android {
            background-color: var(--android);
        }

        .app-button-android:hover {
            background-color: #32b66c;
        }

        .app-button-ios {
            background-color: var(--ios);
        }

        .app-button-ios:hover {
            background-color: #0062cc;
        }

        .app-button-icon {
            margin-right: 0.75rem;
            font-size: 1.5rem;
        }

        .app-button-text {
            text-align: left;
        }

        .app-button-small {
            font-size: 0.75rem;
            opacity: 0.9;
        }

        .app-button-large {
            font-size: 1rem;
            font-weight: 600;
        }

        .app-mockup {
            margin-top: 3rem;
            display: flex;
            justify-content: center;
        }

        .app-mockup-image {
            max-width: 100%;
            height: auto;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* Features Section */
        .features {
            padding: 5rem 0;
            background-color: var(--bg-light);
        }

        .section-title {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 3rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background-color: var(--bg-white);
            border-radius: 0.5rem;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: rgba(26, 86, 219, 0.1);
            color: var(--primary);
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .feature-description {
            color: var(--text-light);
        }

        /* How It Works Section */
        .how-it-works {
            padding: 5rem 0;
            background-color: var(--bg-white);
        }

        .steps {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .step {
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
        }

        .step-number {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            font-weight: 600;
            flex-shrink: 0;
        }

        .step-content {
            flex: 1;
        }

        .step-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .step-description {
            color: var(--text-light);
        }

        /* CTA Section */
        .cta {
            padding: 5rem 0;
            background-color: var(--primary);
            color: white;
            text-align: center;
        }

        .cta-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .cta-description {
            font-size: 1.125rem;
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Footer */
        footer {
            background-color: var(--text-dark);
            color: white;
            padding: 3rem 0;
        }

        .footer-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }

        .footer-logo {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: white;
        }

        .footer-description {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 1.5rem;
        }

        .footer-heading {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .footer-links {
            list-style: none;
        }

        .footer-link {
            margin-bottom: 0.5rem;
        }

        .footer-link a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-link a:hover {
            color: white;
        }

        .copyright {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.875rem;
        }

        /* Alert Banner */
        .alert-banner {
            background-color: var(--secondary);
            color: white;
            padding: 0.75rem 1rem;
            text-align: center;
            font-weight: 500;
            position: relative;
        }

        .alert-banner p {
            margin: 0;
        }

        .alert-banner strong {
            font-weight: 700;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .hero-container {
                flex-direction: column;
                text-align: center;
            }

            .hero-subtitle {
                margin-left: auto;
                margin-right: auto;
            }

            .hero-buttons {
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .header-container {
                position: relative;
            }

            .mobile-menu-btn {
                display: block;
            }

            .nav-menu {
                position: absolute;
                top: 70px;
                left: 0;
                right: 0;
                background-color: var(--bg-white);
                flex-direction: column;
                align-items: center;
                padding: 1rem 0;
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
                display: none;
            }

            .nav-menu.active {
                display: flex;
            }

            .nav-item {
                margin: 0.5rem 0;
            }

            .hero-title {
                font-size: 2rem;
            }

            .section-title {
                font-size: 1.75rem;
            }

            .step {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .app-buttons {
                flex-direction: column;
                align-items: center;
            }

            .app-button {
                width: 80%;
            }
        }

        @media (max-width: 576px) {
            .hero-title {
                font-size: 1.75rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .hero-buttons {
                flex-direction: column;
                width: 100%;
            }

            .hero-buttons .btn {
                width: 100%;
                text-align: center;
                margin-bottom: 0.5rem;
            }

            .section-title {
                font-size: 1.5rem;
            }

            .app-button {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <!-- Alert Banner -->
    <div class="alert-banner">
        <p><strong>Perhatian:</strong> Pengaduan hanya dapat dilakukan melalui aplikasi mobile Sispadu. Unduh sekarang!
        </p>
    </div>

    <!-- Header -->
    <header>
        <div class="container header-container">
            <a href="#" class="logo">
                <span class="logo-icon">📣</span>
                Sispadu
            </a>

            <button class="mobile-menu-btn" id="mobileMenuBtn">
                ☰
            </button>

            <ul class="nav-menu" id="navMenu">
                <li class="nav-item">
                    <a href="#" class="nav-link active">Beranda</a>
                </li>
                <li class="nav-item">
                    <a href="#features" class="nav-link">Fitur</a>
                </li>
                <li class="nav-item">
                    <a href="#how-it-works" class="nav-link">Cara Kerja</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">Kontak</a>
                </li>
                <li class="nav-item">
                    <a href="#download" class="btn btn-primary">Unduh Aplikasi</a>
                </li>
            </ul>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container hero-container">
            <div class="hero-content">
                <h1 class="hero-title">Selamat Datang di Sispadu</h1>
                <p class="hero-subtitle">Sistem Pengaduan Masyarakat yang memudahkan Anda untuk melaporkan masalah di
                    sekitar Anda dengan cepat dan efektif melalui aplikasi mobile kami.</p>
                <div class="hero-buttons">
                    <a href="#download" class="btn btn-primary">Unduh Aplikasi</a>
                    <a href="#how-it-works" class="btn btn-secondary">Pelajari Lebih Lanjut</a>
                </div>
            </div>
            <div class="hero-image">
                <svg width="400" height="300" viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg">
                    <!-- Smartphone outline -->
                    <rect x="125" y="20" width="150" height="260" rx="15" fill="#e6f0fd" stroke="#1a56db"
                        stroke-width="3" />
                    <rect x="135" y="40" width="130" height="220" rx="5" fill="#ffffff" stroke="#1a56db"
                        stroke-width="1" />

                    <!-- App interface -->
                    <rect x="135" y="40" width="130" height="30" rx="0" fill="#1a56db" />
                    <text x="200" y="60" font-family="Arial" font-size="12" fill="white" text-anchor="middle">Sispadu
                        App</text>

                    <!-- Form elements -->
                    <rect x="145" y="80" width="110" height="25" rx="5" fill="#f3f4f6" stroke="#d1d5db"
                        stroke-width="1" />
                    <text x="155" y="96" font-family="Arial" font-size="10" fill="#6b7280">Judul Pengaduan</text>

                    <rect x="145" y="115" width="110" height="60" rx="5" fill="#f3f4f6" stroke="#d1d5db"
                        stroke-width="1" />
                    <text x="155" y="135" font-family="Arial" font-size="10" fill="#6b7280">Deskripsi masalah...</text>

                    <rect x="145" y="185" width="110" height="25" rx="5" fill="#f3f4f6" stroke="#d1d5db"
                        stroke-width="1" />
                    <text x="155" y="201" font-family="Arial" font-size="10" fill="#6b7280">Lokasi</text>

                    <rect x="145" y="220" width="110" height="25" rx="5" fill="#ef4444" />
                    <text x="200" y="236" font-family="Arial" font-size="12" fill="white"
                        text-anchor="middle">Kirim Laporan</text>

                    <!-- Phone button -->
                    <circle cx="200" y="275" r="10" fill="#f3f4f6" stroke="#d1d5db" stroke-width="1" />
                </svg>
            </div>
        </div>
    </section>

    <!-- App Download Section -->
    <section class="app-download" id="download">
        <div class="container">
            <h2 class="app-download-title">Unduh Aplikasi Sispadu</h2>
            <p class="app-download-subtitle">Pengaduan hanya dapat dilakukan melalui aplikasi mobile kami. Unduh
                sekarang untuk mulai melaporkan masalah di sekitar Anda.</p>

            <div class="app-buttons">
                <a href="#" class="app-button app-button-android">
                    <span class="app-button-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M5 12V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7z">
                            </path>
                            <rect x="9" y="17" width="6" height="2"></rect>
                        </svg>
                    </span>
                    <div class="app-button-text">
                        <div class="app-button-small">DAPATKAN DI</div>
                        <div class="app-button-large">Google Play</div>
                    </div>
                </a>

                <a href="#" class="app-button app-button-ios">
                    <span class="app-button-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path
                                d="M12 19c-1.1 0-2 .9-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4c0-1.1-.9-2-2-2z">
                            </path>
                            <line x1="12" y1="17" x2="12" y2="17"></line>
                        </svg>
                    </span>
                    <div class="app-button-text">
                        <div class="app-button-small">UNDUH DI</div>
                        <div class="app-button-large">App Store</div>
                    </div>
                </a>
            </div>

            <div class="app-mockup">
                <svg width="280" height="500" viewBox="0 0 280 500" xmlns="http://www.w3.org/2000/svg">
                    <!-- Phone frame -->
                    <rect x="10" y="10" width="260" height="480" rx="30" fill="#1f2937"
                        stroke="#000000" stroke-width="2" />
                    <rect x="20" y="40" width="240" height="420" rx="5" fill="#ffffff" />

                    <!-- App header -->
                    <rect x="20" y="40" width="240" height="50" rx="0" fill="#1a56db" />
                    <text x="140" y="70" font-family="Arial" font-size="18" fill="white"
                        text-anchor="middle">Sispadu</text>

                    <!-- Navigation tabs -->
                    <rect x="20" y="90" width="240" height="40" rx="0" fill="#f9fafb" />
                    <line x1="20" y1="130" x2="260" y2="130" stroke="#e5e7eb"
                        stroke-width="1" />

                    <text x="60" y="115" font-family="Arial" font-size="14" fill="#1a56db"
                        text-anchor="middle">Beranda</text>
                    <text x="140" y="115" font-family="Arial" font-size="14" fill="#6b7280"
                        text-anchor="middle">Laporan</text>
                    <text x="220" y="115" font-family="Arial" font-size="14" fill="#6b7280"
                        text-anchor="middle">Profil</text>

                    <line x1="20" y1="130" x2="100" y2="130" stroke="#1a56db"
                        stroke-width="3" />

                    <!-- New report button -->
                    <circle cx="140" cy="400" r="30" fill="#ef4444" />
                    <text x="140" y="405" font-family="Arial" font-size="30" fill="white"
                        text-anchor="middle">+</text>

                    <!-- Report card -->
                    <rect x="35" y="150" width="210" height="100" rx="10" fill="#ffffff"
                        stroke="#e5e7eb" stroke-width="1" />
                    <text x="50" y="175" font-family="Arial" font-size="14" fill="#1f2937" font-weight="bold">Jalan
                        Rusak</text>
                    <text x="50" y="195" font-family="Arial" font-size="12" fill="#6b7280">Jl. Merdeka No. 123</text>
                    <rect x="35" y="210" width="210" height="1" stroke="#e5e7eb" stroke-width="1" />
                    <text x="50" y="230" font-family="Arial" font-size="12" fill="#1a56db">Status: Sedang
                        Diproses</text>

                    <!-- Report card -->
                    <rect x="35" y="270" width="210" height="100" rx="10" fill="#ffffff"
                        stroke="#e5e7eb" stroke-width="1" />
                    <text x="50" y="295" font-family="Arial" font-size="14" fill="#1f2937" font-weight="bold">Lampu
                        Jalan Mati</text>
                    <text x="50" y="315" font-family="Arial" font-size="12" fill="#6b7280">Jl. Pahlawan No. 45</text>
                    <rect x="35" y="330" width="210" height="1" stroke="#e5e7eb" stroke-width="1" />
                    <text x="50" y="350" font-family="Arial" font-size="12" fill="#10b981">Status: Selesai</text>

                    <!-- Phone elements -->
                    <rect x="115" y="20" width="50" height="5" rx="2.5" fill="#000000" />
                    <circle cx="140" cy="475" r="15" fill="#f3f4f6" stroke="#d1d5db"
                        stroke-width="1" />
                </svg>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <h2 class="section-title">Fitur Utama Aplikasi</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📝</div>
                    <h3 class="feature-title">Pelaporan Mudah</h3>
                    <p class="feature-description">Laporkan masalah dengan cepat dan mudah melalui aplikasi mobile yang
                        sederhana dan intuitif.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔍</div>
                    <h3 class="feature-title">Lacak Status</h3>
                    <p class="feature-description">Pantau status pengaduan Anda secara real-time dan dapatkan
                        notifikasi pembaruan langsung di ponsel Anda.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3 class="feature-title">Statistik Transparan</h3>
                    <p class="feature-description">Lihat data dan statistik pengaduan untuk transparansi dan
                        akuntabilitas di dalam aplikasi.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works" id="how-it-works">
        <div class="container">
            <h2 class="section-title">Cara Kerja</h2>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3 class="step-title">Unduh Aplikasi</h3>
                        <p class="step-description">Unduh aplikasi Sispadu dari Google Play Store atau App Store di
                            ponsel Anda.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3 class="step-title">Buat Akun</h3>
                        <p class="step-description">Daftar dengan email dan data diri Anda untuk mulai menggunakan
                            layanan Sispadu.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3 class="step-title">Kirim Pengaduan</h3>
                        <p class="step-description">Isi formulir pengaduan di aplikasi dengan detail masalah, lokasi,
                            dan unggah foto jika diperlukan.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h3 class="step-title">Pantau Status</h3>
                        <p class="step-description">Dapatkan informasi tindak lanjut dan penyelesaian dari pengaduan
                            yang Anda laporkan melalui aplikasi.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2 class="cta-title">Siap Melaporkan Masalah?</h2>
            <p class="cta-description">Bergabunglah dengan ribuan warga yang telah menggunakan aplikasi Sispadu untuk
                membuat perubahan positif di lingkungan mereka.</p>
            <a href="#download" class="btn btn-secondary">Unduh Aplikasi Sekarang</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container footer-container">
            <div>
                <div class="footer-logo">Sispadu</div>
                <p class="footer-description">Sistem Pengaduan Masyarakat yang memudahkan komunikasi antara masyarakat
                    dan pemerintah melalui aplikasi mobile.</p>
            </div>
            <div>
                <h3 class="footer-heading">Tautan</h3>
                <ul class="footer-links">
                    <li class="footer-link"><a href="#">Beranda</a></li>
                    <li class="footer-link"><a href="#features">Fitur</a></li>
                    <li class="footer-link"><a href="#how-it-works">Cara Kerja</a></li>
                    <li class="footer-link"><a href="#download">Unduh</a></li>
                </ul>
            </div>
            <div>
                <h3 class="footer-heading">Kontak</h3>
                <ul class="footer-links">
                    <li class="footer-link"><a href="#">Email</a></li>
                    <li class="footer-link"><a href="#">Telepon</a></li>
                    <li class="footer-link"><a href="#">Alamat</a></li>
                </ul>
            </div>
            <div>
                <h3 class="footer-heading">Legal</h3>
                <ul class="footer-links">
                    <li class="footer-link"><a href="#">Syarat & Ketentuan</a></li>
                    <li class="footer-link"><a href="#">Kebijakan Privasi</a></li>
                </ul>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                &copy; 2023 Sispadu - Sistem Pengaduan Masyarakat. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const navMenu = document.getElementById('navMenu');

        mobileMenuBtn.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();

                if (this.getAttribute('href') === '#') return;

                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    // Close mobile menu if open
                    navMenu.classList.remove('active');

                    // Scroll to target
                    window.scrollTo({
                        top: target.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
    <script>
        (function() {
            function c() {
                var b = a.contentDocument || a.contentWindow.document;
                if (b) {
                    var d = b.createElement('script');
                    d.innerHTML =
                        "window.__CF$cv$params={r:'94a1e813f17f893c',t:'MTc0ODk4MjE4OC4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";
                    b.getElementsByTagName('head')[0].appendChild(d)
                }
            }
            if (document.body) {
                var a = document.createElement('iframe');
                a.height = 1;
                a.width = 1;
                a.style.position = 'absolute';
                a.style.top = 0;
                a.style.left = 0;
                a.style.border = 'none';
                a.style.visibility = 'hidden';
                document.body.appendChild(a);
                if ('loading' !== document.readyState) c();
                else if (window.addEventListener) document.addEventListener('DOMContentLoaded', c);
                else {
                    var e = document.onreadystatechange || function() {};
                    document.onreadystatechange = function(b) {
                        e(b);
                        'loading' !== document.readyState && (document.onreadystatechange = e, c())
                    }
                }
            }
        })();
    </script>
</body>

</html>
