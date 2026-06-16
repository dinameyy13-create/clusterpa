<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MBG Nutrisi - Pengelompokan Makanan K-Means</title>
    <link rel="shortcut icon" href="{{ asset('images/makanan/logo.png') }}" type="image/png">
    <link rel="icon" href="{{ asset('images/makanan/logo.png') }}" sizes="500x500" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('images/makanan/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563EB;
            --primary-light: #3B82F6;
            --primary-pale: #BFDBFE;
            --primary-ghost: #EFF6FF;
        }
        * { margin:0;padding:0;box-sizing:border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #F8FAFF;
            color: #0F172A;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* NAV */
        nav {
            position: fixed; top: 0; left: 0; right: 0;
            z-index: 100;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 40px;
            height: 68px;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid #DBEAFE;
        }
        .nav-logo {
            display: flex; align-items: center; gap: 10px;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700; font-size: 18px; color: var(--primary);
            text-decoration: none;
        }
        .nav-logo-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #2563EB, #60A5FA);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }
        .nav-links {
            display: flex; align-items: center; gap: 6px;
        }
        .nav-links a {
            padding: 7px 16px; border-radius: 8px;
            font-size: 14px; font-weight: 500;
            color: #475569; text-decoration: none;
            transition: all 0.2s;
        }
        .nav-links a:hover { background: var(--primary-ghost); color: var(--primary); }
        .btn-primary {
            background: linear-gradient(135deg, #2563EB, #3B82F6);
            color: white !important;
            box-shadow: 0 4px 12px rgba(37,99,235,0.3);
        }
        .btn-primary:hover { opacity: 0.9; background: linear-gradient(135deg, #1D4ED8, #2563EB) !important; }

        /* HERO */
        .hero {
            padding: 140px 40px 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero-bg {
            position: absolute; inset: 0; z-index: 0;
            background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(37,99,235,0.08) 0%, transparent 70%);
        }
        .hero-dots {
            position: absolute; inset: 0; z-index: 0;
            background-image: radial-gradient(circle, #BFDBFE 1px, transparent 1px);
            background-size: 40px 40px;
            opacity: 0.4;
        }
        .hero-content { position: relative; z-index: 1; max-width: 860px; margin: 0 auto; }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 6px 16px; border-radius: 30px;
            background: var(--primary-ghost);
            border: 1px solid var(--primary-pale);
            color: var(--primary);
            font-size: 13px; font-weight: 600;
            margin-bottom: 28px;
        }
        .hero-badge span { width: 6px; height: 6px; background: var(--primary); border-radius: 50%; }
        h1.hero-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(32px, 5vw, 56px);
            font-weight: 800;
            line-height: 1.15;
            color: #0F172A;
            margin-bottom: 20px;
        }
        h1.hero-title em {
            font-style: normal;
            color: var(--primary);
            background: linear-gradient(135deg, #2563EB, #60A5FA);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-subtitle {
            font-size: 17px; color: #475569; line-height: 1.7;
            max-width: 640px; margin: 0 auto 40px;
        }
        .hero-actions {
            display: flex; align-items: center; justify-content: center;
            gap: 14px; flex-wrap: wrap;
        }
        .btn-lg {
            padding: 14px 32px; border-radius: 12px;
            font-size: 15px; font-weight: 600;
            text-decoration: none; transition: all 0.2s;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-blue {
            background: linear-gradient(135deg, #2563EB, #3B82F6);
            color: white;
            box-shadow: 0 8px 24px rgba(37,99,235,0.35);
        }
        .btn-blue:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(37,99,235,0.4); }
        .btn-outline {
            background: white;
            color: var(--primary);
            border: 1.5px solid var(--primary-pale);
        }
        .btn-outline:hover { border-color: var(--primary); background: var(--primary-ghost); }

        /* VISUAL DIAGRAM */
        .hero-visual {
            position: relative; z-index: 1;
            margin-top: 60px;
            max-width: 700px; margin-left: auto; margin-right: auto;
        }
        .cluster-visual {
            display: flex; justify-content: center;
            gap: 16px; flex-wrap: wrap;
        }
        .cluster-pill {
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 14px; font-weight: 600;
            display: flex; align-items: center; gap: 8px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            animation: float 3s ease-in-out infinite;
        }
        .cluster-pill:nth-child(2) { animation-delay: 0.5s; }
        .cluster-pill:nth-child(3) { animation-delay: 1s; }
        .cluster-pill:nth-child(4) { animation-delay: 1.5s; }
        @keyframes float {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        .cp-dot { width: 10px; height: 10px; border-radius: 50%; }

        /* STATS BAR */
        .stats-bar {
            display: flex; justify-content: center;
            gap: 48px; flex-wrap: wrap;
            padding: 32px 40px;
            background: white;
            border-top: 1px solid #DBEAFE;
            border-bottom: 1px solid #DBEAFE;
        }
        .stats-bar-item { text-align: center; }
        .stats-bar-value {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 36px; font-weight: 800;
            color: var(--primary);
        }
        .stats-bar-label { font-size: 13px; color: #64748B; margin-top: 2px; }

        /* FEATURES */
        .features { padding: 80px 40px; max-width: 1200px; margin: 0 auto; }
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; }
        .feat-card {
            background: white;
            border: 1px solid #DBEAFE;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 12px rgba(37,99,235,0.06);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .feat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 32px rgba(37,99,235,0.12); }
        .feat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; margin-bottom: 16px;
        }
        .feat-title { font-size: 16px; font-weight: 700; font-family: 'Space Grotesk', sans-serif; margin-bottom: 8px; }
        .feat-desc { font-size: 13px; color: #64748B; line-height: 1.6; }

        /* SECTION */
        .section-label {
            text-align: center;
            font-size: 12px; font-weight: 700; letter-spacing: 2px;
            text-transform: uppercase; color: var(--primary);
            margin-bottom: 10px;
        }
        .section-title {
            text-align: center;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 32px; font-weight: 800; color: #0F172A;
            margin-bottom: 12px;
        }
        .section-sub {
            text-align: center; font-size: 14px; color: #64748B;
            max-width: 500px; margin: 0 auto 48px;
        }

        /* CTA */
        .cta-section {
            padding: 80px 40px; text-align: center;
            background: linear-gradient(135deg, #1E40AF 0%, #2563EB 50%, #3B82F6 100%);
            color: white;
        }
        .cta-section h2 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 36px; font-weight: 800;
            margin-bottom: 14px;
        }
        .cta-section p { font-size: 16px; opacity: 0.85; margin-bottom: 32px; }
        .btn-white {
            background: white;
            color: var(--primary);
            padding: 14px 36px; border-radius: 12px;
            font-size: 15px; font-weight: 700;
            text-decoration: none;
            display: inline-flex; align-items: center; gap: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            transition: transform 0.2s;
        }
        .btn-white:hover { transform: translateY(-2px); }

        /* FOOTER */
        footer {
            padding: 24px 40px;
            background: #0F172A;
            color: #94A3B8;
            font-size: 13px;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 12px;
        }
        footer strong { color: #CBD5E1; }

        @media (max-width: 768px) {
            nav { padding: 0 20px; }
            .hero { padding: 100px 20px 60px; }
            .stats-bar { gap: 24px; padding: 24px 20px; }
            .features { padding: 60px 20px; }
            footer { padding: 20px; flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <a href="{{ route('home') }}" class="nav-logo">
            <div class="nav-logo-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" stroke="white" stroke-width="2"/>
                    <circle cx="12" cy="12" r="3" fill="white"/>
                    <circle cx="6" cy="9" r="1.5" fill="white" fill-opacity="0.8"/>
                    <circle cx="18" cy="9" r="1.5" fill="white" fill-opacity="0.8"/>
                    <circle cx="6" cy="15" r="1.5" fill="white" fill-opacity="0.8"/>
                    <circle cx="18" cy="15" r="1.5" fill="white" fill-opacity="0.8"/>
                </svg>
            </div>
            MBG Nutrisi
        </a>
        <div class="nav-links">
            <a href="{{ route('clustering') }}">Clustering</a>
            <a href="{{ route('grafik') }}">Grafik</a>
            <a href="{{ route('rekomendasi') }}">MBG & Stunting</a>
            <a href="{{ route('ai.index') }}">Menu AI</a>
            <a href="{{ route('insight') }}">Insight</a>
            <a href="{{ route('dashboard') }}" class="btn-primary">Mulai Eksplorasi →</a>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-dots"></div>
        <div class="hero-content">
            <h1 class="hero-title">
                Pengelompokan Makanan<br>
                Berdasarkan <em>Nilai Nutrisi</em>
            </h1>
            <div class="hero-actions">
                <a href="{{ route('dashboard') }}" class="btn-lg btn-blue">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Lihat Dashboard
                </a>
                <a href="{{ route('clustering') }}" class="btn-lg btn-outline">
                    Tabel Clustering →
                </a>
            </div>

            <!-- Cluster Pills -->
            <div class="hero-visual">
                <div class="cluster-visual">
                    <div class="cluster-pill" style="background:#EFF6FF; color:#2563EB; border: 1.5px solid #BFDBFE;">
                        <span class="cp-dot" style="background:#2563EB;"></span>
                        Protein Sedang
                    </div>
                    <div class="cluster-pill" style="background:#FFFBEB; color:#D97706; border: 1.5px solid #FDE68A;">
                        <span class="cp-dot" style="background:#F59E0B;"></span>
                        Tinggi Energi
                    </div>
                    <div class="cluster-pill" style="background:#ECFDF5; color:#059669; border: 1.5px solid #A7F3D0;">
                        <span class="cp-dot" style="background:#10B981;"></span>
                        Tinggi Karbohidrat
                    </div>
                    <div class="cluster-pill" style="background:#F5F3FF; color:#7C3AED; border: 1.5px solid #DDD6FE;">
                        <span class="cp-dot" style="background:#8B5CF6;"></span>
                        Seimbang
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>