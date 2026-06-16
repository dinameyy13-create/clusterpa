<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MBG Nutrisi') | Pengelompokan Makanan K-Means</title>
    <link rel="shortcut icon" href="{{ asset('images/makanan/logo.png') }}" type="image/png">
    <link rel="icon" href="{{ asset('images/makanan/logo.png') }}" sizes="500x500" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('images/makanan/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')

<style>

/* ==============================
   MBG NUTRISI - MAIN STYLESHEET
   Biru Muda / Light Blue Theme
================================ */

:root {
    --primary: #2563EB;
    --primary-light: #3B82F6;
    --primary-lighter: #60A5FA;
    --primary-pale: #BFDBFE;
    --primary-ghost: #EFF6FF;
    --primary-dark: #1D4ED8;

    --cluster-0: #2563EB;
    --cluster-1: #F59E0B;
    --cluster-2: #10B981;
    --cluster-3: #8B5CF6;

    --surface: #FFFFFF;
    --surface-2: #F8FAFF;
    --surface-3: #F0F7FF;
    --border: #DBEAFE;
    --border-2: #BFDBFE;

    --text-primary: #0F172A;
    --text-secondary: #475569;
    --text-muted: #94A3B8;

    --sidebar-w: 260px;
    --header-h: 64px;

    --shadow-sm: 0 1px 3px rgba(37,99,235,0.08), 0 1px 2px rgba(37,99,235,0.04);
    --shadow-md: 0 4px 20px rgba(37,99,235,0.1), 0 2px 8px rgba(37,99,235,0.06);
    --shadow-lg: 0 10px 40px rgba(37,99,235,0.15);

    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 24px;
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: var(--surface-2);
    color: var(--text-primary);
    min-height: 100vh;
    display: flex;
}

/* ========== SIDEBAR ========== */
.sidebar {
    width: var(--sidebar-w);
    height: 100vh;
    position: fixed;
    left: 0; top: 0;
    background: linear-gradient(160deg, #1E40AF 0%, #2563EB 50%, #3B82F6 100%);
    display: flex;
    flex-direction: column;
    z-index: 100;
    transition: transform 0.3s ease;
    box-shadow: 4px 0 24px rgba(37,99,235,0.25);
}

.sidebar-logo {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px 20px 16px;
    border-bottom: 1px solid rgba(255,255,255,0.15);
}

.logo-icon {
    width: 44px; height: 44px;
    background: rgba(255,255,255,0.2);
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    backdrop-filter: blur(10px);
}

.logo-title {
    display: block;
    font-size: 16px;
    font-weight: 700;
    color: white;
    font-family: 'Space Grotesk', sans-serif;
}

.logo-sub {
    display: block;
    font-size: 10px;
    color: rgba(255,255,255,0.65);
    font-weight: 500;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.sidebar-nav {
    flex: 1;
    padding: 16px 12px;
    overflow-y: auto;
}

.nav-group {
    margin-bottom: 20px;
}

.nav-group-label {
    display: block;
    font-size: 10px;
    font-weight: 700;
    color: rgba(255,255,255,0.45);
    letter-spacing: 1.2px;
    text-transform: uppercase;
    padding: 0 8px;
    margin-bottom: 6px;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: var(--radius-sm);
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
    margin-bottom: 2px;
}

.nav-item svg {
    width: 18px; height: 18px;
    flex-shrink: 0;
    opacity: 0.8;
}

.nav-item:hover {
    background: rgba(255,255,255,0.15);
    color: white;
}

.nav-item.active {
    background: white;
    color: var(--primary);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.nav-item.active svg { opacity: 1; }

.sidebar-footer {
    padding: 16px 20px;
    border-top: 1px solid rgba(255,255,255,0.15);
}

.sidebar-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: rgba(255,255,255,0.7);
}

.badge-dot {
    width: 8px; height: 8px;
    background: #10B981;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}

/* ========== MAIN WRAPPER ========== */
.main-wrapper {
    margin-left: var(--sidebar-w);
    flex: 1;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* ========== TOPBAR ========== */
.topbar {
    height: var(--header-h);
    background: white;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 0 24px;
    position: sticky;
    top: 0;
    z-index: 50;
    box-shadow: var(--shadow-sm);
}

.sidebar-toggle {
    background: none;
    border: none;
    cursor: pointer;
    padding: 8px;
    border-radius: var(--radius-sm);
    color: var(--text-secondary);
    display: none;
}

.topbar-title { flex: 1; }

.topbar-title h1 {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    font-family: 'Space Grotesk', sans-serif;
}

.breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 2px;
}

.breadcrumb a {
    color: var(--primary);
    text-decoration: none;
}

.breadcrumb span { color: var(--text-muted); }

.topbar-right { display: flex; align-items: center; gap: 12px; }

.topbar-info {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.info-label {
    font-size: 10px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 13px;
    font-weight: 600;
    color: var(--primary);
}

/* ========== PAGE CONTENT ========== */
.page-content {
    flex: 1;
    padding: 24px;
    background: var(--surface-2);
}

/* ========== CARDS ========== */
.card {
    background: white;
    border-radius: var(--radius-lg);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}

.card-header {
    padding: 20px 24px 16px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.card-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-primary);
    font-family: 'Space Grotesk', sans-serif;
}

.card-subtitle {
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 2px;
}

.card-body { padding: 24px; }

/* ========== STAT CARDS ========== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.stat-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: 20px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
    position: relative;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary), var(--primary-lighter));
}

.stat-icon {
    width: 44px; height: 44px;
    background: var(--primary-ghost);
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 14px;
}

.stat-icon svg {
    width: 22px; height: 22px;
    color: var(--primary);
}

.stat-value {
    font-size: 32px;
    font-weight: 800;
    color: var(--text-primary);
    font-family: 'Space Grotesk', sans-serif;
    line-height: 1;
}

.stat-label {
    font-size: 13px;
    color: var(--text-secondary);
    margin-top: 4px;
    font-weight: 500;
}

.stat-change {
    font-size: 11px;
    color: #10B981;
    background: #ECFDF5;
    padding: 2px 8px;
    border-radius: 20px;
    margin-top: 8px;
    display: inline-block;
    font-weight: 600;
}

/* ========== CLUSTER BADGES ========== */
.cluster-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.cluster-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
}

/* ========== TABLE ========== */
.data-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 13px;
}

.data-table thead th {
    padding: 12px 16px;
    background: var(--surface-3);
    color: var(--text-secondary);
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
}

.data-table thead th:first-child { border-radius: var(--radius-sm) 0 0 0; }
.data-table thead th:last-child  { border-radius: 0 var(--radius-sm) 0 0; }

.data-table tbody tr {
    transition: background 0.15s;
}

.data-table tbody tr:hover { background: var(--primary-ghost); }

.data-table tbody td {
    padding: 12px 16px;
    border-bottom: 1px solid var(--border);
    color: var(--text-primary);
    vertical-align: middle;
}

/* ========== FILTERS ========== */
.filter-bar {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
    flex-wrap: wrap;
    align-items: center;
}

.filter-input {
    padding: 9px 14px;
    border: 1.5px solid var(--border-2);
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-family: inherit;
    outline: none;
    transition: border-color 0.2s;
    background: white;
    color: var(--text-primary);
}

.filter-input:focus { border-color: var(--primary); }

.filter-select {
    padding: 9px 14px;
    border: 1.5px solid var(--border-2);
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-family: inherit;
    outline: none;
    cursor: pointer;
    background: white;
    color: var(--text-primary);
    transition: border-color 0.2s;
}

.filter-select:focus { border-color: var(--primary); }

/* ========== GRID LAYOUTS ========== */
.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
.grid-auto { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }

/* ========== SECTION HEADER ========== */
.section-header {
    margin-bottom: 20px;
}

.section-title {
    font-size: 22px;
    font-weight: 800;
    color: var(--text-primary);
    font-family: 'Space Grotesk', sans-serif;
}

.section-desc {
    font-size: 13px;
    color: var(--text-secondary);
    margin-top: 4px;
}

/* ========== CHART CONTAINER ========== */
.chart-wrap {
    position: relative;
    height: 300px;
}

.chart-wrap-tall { height: 380px; }

/* ========== INSIGHT CARDS ========== */
.insight-card {
    background: white;
    border-radius: var(--radius-lg);
    border: 1px solid var(--border);
    padding: 20px;
    box-shadow: var(--shadow-sm);
    position: relative;
    overflow: hidden;
}

.insight-card::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0;
    width: 100%; height: 3px;
}

.insight-card.c0::after { background: var(--cluster-0); }
.insight-card.c1::after { background: var(--cluster-1); }
.insight-card.c2::after { background: var(--cluster-2); }
.insight-card.c3::after { background: var(--cluster-3); }

.insight-header {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 14px;
}

.insight-icon {
    width: 40px; height: 40px;
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.insight-title { font-size: 15px; font-weight: 700; font-family: 'Space Grotesk', sans-serif; }
.insight-count { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
.insight-desc { font-size: 13px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 14px; }

.food-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.food-chip {
    padding: 3px 10px;
    background: var(--surface-3);
    border: 1px solid var(--border);
    border-radius: 20px;
    font-size: 11px;
    font-weight: 500;
    color: var(--text-secondary);
}

/* ========== LOADING STATE ========== */
.loading {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 200px;
    color: var(--text-muted);
    gap: 8px;
    font-size: 13px;
}

.spinner {
    width: 20px; height: 20px;
    border: 2px solid var(--border);
    border-top-color: var(--primary);
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

/* ========== RESPONSIVE ========== */

@media (max-width: 1024px) {

    .grid-2,
    .grid-3 {
        grid-template-columns: 1fr;
    }

    .page-content{
        padding:20px;
    }

}

@media (max-width:768px){

    /* Sidebar */

    .sidebar{
        transform:translateX(-100%);
    }

    .sidebar.open{
        transform:translateX(0);
    }

    .main-wrapper{
        margin-left:0;
        width:100%;
    }

    .sidebar-toggle{
        display:flex;
    }

    .page-content{
        padding:14px;
        overflow-x:hidden;
    }

    /* Topbar */

    .topbar{
        padding:0 14px;
    }

    .topbar-title h1{
        font-size:16px;
    }

    .topbar-info{
        display:none;
    }

    /* Stats */

    .stats-grid{
        grid-template-columns:1fr;
    }

    /* Card */

    .card-header{
        flex-direction:column;
        align-items:flex-start;
        gap:10px;
    }

    .card-body{
        padding:16px;
    }

    /* Filter */

    .filter-bar{
        flex-direction:column;
        align-items:stretch;
    }

    .filter-bar>*{
        width:100% !important;
        max-width:100% !important;
    }

    .filter-input,
    .filter-select,
    button,
    select{
        width:100%;
    }

    /* Table */

    .table-scroll{
        width:100%;
        overflow-x:auto;
        -webkit-overflow-scrolling:touch;
    }

    .data-table{
        min-width:1100px;
    }

    /* Section */

    .section-title{
        font-size:20px;
    }

    .section-desc{
        font-size:13px;
    }

}

@media (max-width:480px){

    .page-content{
        padding:10px;
    }

    .card-header{
        padding:14px;
    }

    .card-body{
        padding:14px;
    }

    .stat-card{
        padding:16px;
    }

    .stat-value{
        font-size:26px;
    }

    .section-title{
        font-size:18px;
    }

}
/* ========== SCROLLBAR ========== */
::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--border-2); border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: var(--primary-lighter); }

</style>

</head>
<body>
    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                    <circle cx="14" cy="14" r="13" stroke="white" stroke-width="2"/>
                    <path d="M8 14 C8 10, 11 8, 14 8 C17 8, 20 10, 20 14 C20 18, 17 20, 14 20 C11 20, 8 18, 8 14Z" fill="white" fill-opacity="0.3"/>
                    <circle cx="14" cy="14" r="3" fill="white"/>
                    <circle cx="9" cy="11" r="1.5" fill="white" fill-opacity="0.7"/>
                    <circle cx="19" cy="11" r="1.5" fill="white" fill-opacity="0.7"/>
                    <circle cx="9" cy="17" r="1.5" fill="white" fill-opacity="0.7"/>
                    <circle cx="19" cy="17" r="1.5" fill="white" fill-opacity="0.7"/>
                </svg>
            </div>
            <div class="logo-text">
                <span class="logo-title">MBG Nutrisi</span>
                <span class="logo-sub">K-Means Clustering</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-group">
                <span class="nav-group-label">Menu Utama</span>
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="nav-group">
                <span class="nav-group-label">Analisis</span>
                <a href="{{ route('clustering') }}" class="nav-item {{ request()->routeIs('clustering') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><circle cx="3" cy="6" r="2"/><circle cx="21" cy="6" r="2"/><circle cx="3" cy="18" r="2"/><circle cx="21" cy="18" r="2"/><path d="M5 6h14M5 18h14M6 8l5 3m2 0 5-3M6 16l5-3m2 0 5 3"/></svg>
                    <span>Tabel Clustering</span>
                </a>
                <a href="{{ route('grafik') }}" class="nav-item {{ request()->routeIs('grafik') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    <span>Grafik Clustering</span>
                </a>
                <a href="{{ route('rekomendasi') }}" class="nav-item {{ request()->routeIs('rekomendasi') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                    </svg>
                    <span>Menu MBG & Sunting</span>
                </a>
                <a href="{{ route('ai.index') }}" class="nav-item {{ request()->routeIs('ai.index') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    <span>Rekomendasi AI</span>
                </a>
                <a href="{{ route('insight') }}" class="nav-item {{ request()->routeIs('insight') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.663 17h4.673M12 3v1m6.364 1.636-.707.707M21 12h-1M4 12H3m3.343-5.657-.707-.707m2.828 9.9a5 5 0 1 1 7.072 0l-.548.547A3.374 3.374 0 0 0 14 18.469V19a2 2 0 1 1-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    <span>Insight MBG & Stunting</span>
                </a>
            </div>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-badge">
                <span class="badge-dot"></span>
                <span>Sistem Aktif</span>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-wrapper">
        <!-- Top Header -->
        <header class="topbar">
            <button class="sidebar-toggle" id="sidebarToggle">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            <div class="topbar-title">
                <h1>@yield('page-title', 'Dashboard')</h1>
                <nav class="breadcrumb">
                    <a href="{{ route('home') }}">Beranda</a>
                    @hasSection('breadcrumb')
                        <span>/</span>
                        @yield('breadcrumb')
                    @endif
                </nav>
            </div>
        </header>

        <!-- Page Content -->
        <main class="page-content">
            @yield('content')
        </main>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>