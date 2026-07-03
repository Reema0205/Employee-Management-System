<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Medi Core — Admin Panel</title>
  <style>

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --primary:        #0ea5e9;
      --primary-dark:   #0284c7;
      --secondary:      #8b5cf6;
      --accent:         #10b981;
      --amber:          #f59e0b;
      --danger:         #ef4444;

      --bg-base:        #0a0f1e;
      --bg-surface:     #0f1629;
      --bg-card:        #131d35;
      --bg-elevated:    #1a2545;

      --border-color:   rgba(255,255,255,0.08);
      --border-active:  rgba(14,165,233,0.35);

      --text-primary:   #f0f4ff;
      --text-secondary: #94a3b8;
      --text-muted:     #4b6080;

      --radius-sm:      6px;
      --radius-md:      10px;
      --radius-lg:      16px;
      --radius-xl:      24px;

      --shadow-sm:      0 2px 8px rgba(0,0,0,0.3);
      --shadow-md:      0 4px 20px rgba(0,0,0,0.4);
      --shadow-lg:      0 8px 40px rgba(0,0,0,0.5);

      --font-sans:      'Segoe UI', system-ui, -apple-system, sans-serif;
      --font-display:   'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    html, body {
      height: 100%;
      background: var(--bg-base);
      color: var(--text-primary);
      font-family: var(--font-sans);
      font-size: 15px;
      line-height: 1.6;
    }

    a { color: inherit; text-decoration: none; }


    #login-screen {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 24px;
      background: radial-gradient(ellipse at 20% 50%, rgba(14,165,233,0.08) 0%, transparent 60%),
                  radial-gradient(ellipse at 80% 20%, rgba(139,92,246,0.08) 0%, transparent 60%),
                  var(--bg-base);
    }

    .login-card {
      background: var(--bg-surface);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-xl);
      padding: 48px 40px;
      width: 100%;
      max-width: 440px;
      box-shadow: var(--shadow-lg);
      text-align: center;
    }

    .login-logo {
      width: 72px; height: 72px;
      background: rgba(14,165,233,0.12);
      border: 1px solid rgba(14,165,233,0.25);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      color: var(--primary);
      margin: 0 auto 24px;
    }

    .login-card h2 {
      font-size: 24px; font-weight: 800;
      margin-bottom: 8px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .login-card p {
      color: var(--text-secondary);
      font-size: 14px;
      margin-bottom: 32px;
    }

    .form-group { margin-bottom: 18px; text-align: left; }

    .form-label {
      display: block;
      font-size: 13px; font-weight: 600;
      color: var(--text-secondary);
      margin-bottom: 6px;
      text-transform: uppercase; letter-spacing: 0.5px;
    }

    .form-control {
      width: 100%;
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      color: var(--text-primary);
      font-size: 14px;
      padding: 11px 14px;
      outline: none;
      transition: border-color .2s;
    }

    .form-control:focus { border-color: var(--border-active); }

    select.form-control option { background: var(--bg-card); }

    .btn {
      display: inline-flex; align-items: center; justify-content: center; gap: 6px;
      padding: 10px 20px;
      border: none; border-radius: var(--radius-md);
      font-size: 14px; font-weight: 600; cursor: pointer;
      transition: opacity .2s, transform .1s;
    }
    .btn:hover   { opacity: .88; }
    .btn:active  { transform: scale(.97); }

    .btn-primary   { background: var(--primary);   color: #fff; }
    .btn-secondary { background: var(--bg-elevated); color: var(--text-primary); border: 1px solid var(--border-color); }
    .btn-danger    { background: var(--danger);     color: #fff; }
    .btn-full      { width: 100%; }

    #login-error {
      color: var(--danger);
      font-size: 13px; font-weight: 600;
      margin-bottom: 14px;
      display: none;
    }

    #admin-app {
      display: none;
      min-height: 100vh;
      flex-direction: column;
    }

    .topbar {
      height: 60px;
      background: var(--bg-surface);
      border-bottom: 1px solid var(--border-color);
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 28px;
      position: sticky; top: 0; z-index: 100;
    }

    .topbar-logo {
      display: flex; align-items: center; gap: 10px;
      font-size: 18px; font-weight: 800;
    }
    .topbar-logo svg { color: var(--primary); }
    .topbar-logo span { color: var(--primary); }

    .topbar-right {
      display: flex; align-items: center; gap: 14px;
    }

    .admin-badge {
      background: rgba(14,165,233,0.1);
      border: 1px solid rgba(14,165,233,0.2);
      border-radius: 50px;
      padding: 5px 14px;
      font-size: 13px; font-weight: 700; color: var(--primary);
      display: flex; align-items: center; gap: 7px;
    }

    .btn-logout {
      background: rgba(239,68,68,0.1);
      border: 1px solid rgba(239,68,68,0.2);
      color: var(--danger);
      border-radius: var(--radius-md);
      padding: 7px 14px;
      font-size: 13px; font-weight: 600; cursor: pointer;
      display: flex; align-items: center; gap: 6px;
      transition: background .2s;
    }
    .btn-logout:hover { background: rgba(239,68,68,0.18); }


    .app-body {
      display: flex;
      flex: 1;
    }


    .sidebar {
      width: 220px;
      min-height: calc(100vh - 60px);
      background: var(--bg-surface);
      border-right: 1px solid var(--border-color);
      padding: 24px 12px;
      position: sticky; top: 60px;
      display: flex; flex-direction: column; gap: 4px;
    }

    .sidebar-label {
      font-size: 10px; font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase; letter-spacing: 1px;
      padding: 0 12px; margin-bottom: 8px; margin-top: 16px;
    }
    .sidebar-label:first-child { margin-top: 0; }

    .nav-item {
      display: flex; align-items: center; gap: 10px;
      padding: 10px 12px;
      border-radius: var(--radius-md);
      font-size: 14px; font-weight: 500;
      color: var(--text-secondary);
      cursor: pointer;
      transition: background .15s, color .15s;
    }
    .nav-item:hover  { background: var(--bg-elevated); color: var(--text-primary); }
    .nav-item.active { background: rgba(14,165,233,0.12); color: var(--primary); font-weight: 700; }
    .nav-item svg    { flex-shrink: 0; }

    .nav-item.danger       { color: var(--danger); }
    .nav-item.danger:hover { background: rgba(239,68,68,0.1); }

    .main-content {
      flex: 1;
      padding: 28px;
      overflow-y: auto;
    }

    .panel { display: none; }
    .panel.active { display: block; }

    .page-title {
      font-size: 22px; font-weight: 800;
      margin-bottom: 6px;
    }
    .page-sub {
      color: var(--text-secondary); font-size: 14px;
      margin-bottom: 28px;
    }

    .metrics-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
      margin-bottom: 28px;
    }

    .metric-card {
      background: var(--bg-surface);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      padding: 20px;
      display: flex; align-items: center; gap: 16px;
    }

    .metric-icon {
      width: 48px; height: 48px;
      border-radius: var(--radius-md);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .metric-icon.blue   { background: rgba(14,165,233,0.12); color: var(--primary); }
    .metric-icon.purple { background: rgba(139,92,246,0.12); color: var(--secondary); }
    .metric-icon.green  { background: rgba(16,185,129,0.12); color: var(--accent); }
    .metric-icon.amber  { background: rgba(245,158,11,0.12); color: var(--amber); }

    .metric-info h4 {
      font-size: 12px; font-weight: 600;
      color: var(--text-secondary);
      text-transform: uppercase; letter-spacing: 0.5px;
      margin-bottom: 4px;
    }
    .metric-val {
      font-size: 22px; font-weight: 800;
      color: var(--text-primary);
    }

    .card {
      background: var(--bg-surface);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      padding: 24px;
      margin-bottom: 24px;
    }

    .card-title {
      font-size: 16px; font-weight: 700;
      margin-bottom: 20px;
      display: flex; align-items: center; gap: 8px;
    }

    .table-wrapper { overflow-x: auto; }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px 14px;
      text-align: left;
      border-bottom: 1px solid var(--border-color);
      font-size: 14px;
    }

    th {
      font-size: 11px; font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase; letter-spacing: 0.6px;
      background: var(--bg-card);
    }

    tr:last-child td { border-bottom: none; }
    tr:hover td      { background: rgba(255,255,255,0.02); }

    .doc-cell {
      display: flex; align-items: center; gap: 12px;
    }

    .doc-avatar {
      width: 36px; height: 36px;
      border-radius: 50%;
      background: var(--bg-elevated);
      display: flex; align-items: center; justify-content: center;
      color: var(--primary);
      flex-shrink: 0;
      overflow: hidden;
    }

    .doc-name   { font-weight: 600; font-size: 14px; }
    .doc-id     { font-size: 11px; color: var(--text-muted); }

    .badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 50px;
      font-size: 11px; font-weight: 700;
    }
    .badge-blue   { background: rgba(14,165,233,0.15); color: var(--primary); }
    .badge-purple { background: rgba(139,92,246,0.15); color: var(--secondary); }
    .badge-green  { background: rgba(16,185,129,0.15); color: var(--accent); }

    .action-btn {
      width: 30px; height: 30px;
      border-radius: var(--radius-sm);
      border: 1px solid var(--border-color);
      background: var(--bg-elevated);
      color: var(--text-secondary);
      cursor: pointer;
      display: inline-flex; align-items: center; justify-content: center;
      transition: background .15s, color .15s;
      margin-right: 4px;
    }
    .action-btn:hover           { background: var(--primary); color: #fff; border-color: var(--primary); }
    .action-btn.del:hover       { background: var(--danger); color: #fff; border-color: var(--danger); }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    @media (max-width: 640px) {
      .form-row { grid-template-columns: 1fr; }
    }

    textarea.form-control { resize: vertical; min-height: 90px; }

    .form-actions {
      text-align: right;
      margin-top: 20px;
    }

    .empty-state {
      text-align: center;
      padding: 48px 20px;
      color: var(--text-muted);
    }
    .empty-state svg { margin-bottom: 12px; opacity: .4; }

    .loader {
      display: inline-block;
      width: 20px; height: 20px;
      border: 2px solid rgba(255,255,255,0.1);
      border-top-color: var(--primary);
      border-radius: 50%;
      animation: spin .7s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    #toast {
      position: fixed; bottom: 28px; right: 28px;
      padding: 12px 20px;
      border-radius: var(--radius-md);
      font-size: 14px; font-weight: 600;
      box-shadow: var(--shadow-lg);
      z-index: 9999;
      opacity: 0;
      transform: translateY(12px);
      transition: opacity .3s, transform .3s;
      pointer-events: none;
    }
    #toast.show     { opacity: 1; transform: translateY(0); }
    #toast.success  { background: var(--accent); color: #fff; }
    #toast.error    { background: var(--danger); color: #fff; }
    #toast.info     { background: var(--primary); color: #fff; }

    .modal-overlay {
      display: none;
      position: fixed; inset: 0;
      background: rgba(0,0,0,0.7);
      z-index: 500;
      align-items: center; justify-content: center;
      padding: 24px;
    }
    .modal-overlay.active { display: flex; }

    .modal-box {
      background: var(--bg-surface);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-xl);
      padding: 32px;
      width: 100%; max-width: 560px;
      box-shadow: var(--shadow-lg);
      max-height: 90vh;
      overflow-y: auto;
    }

    .modal-title {
      font-size: 18px; font-weight: 800;
      margin-bottom: 20px;
      display: flex; align-items: center; justify-content: space-between;
    }

    .modal-close {
      background: none; border: none;
      color: var(--text-muted); cursor: pointer;
      font-size: 20px; line-height: 1;
      padding: 4px;
    }
    .modal-close:hover { color: var(--text-primary); }

    .quick-actions {
      display: flex; gap: 12px; flex-wrap: wrap;
      margin-top: 16px;
    }

    .rating-star { color: var(--amber); font-weight: 700; }

    @media (max-width: 768px) {
      .sidebar { display: none; }
    }
  </style>
</head>
<body>

<div id="login-screen">
  <div class="login-card">
    <div class="login-logo">
      <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
      </svg>
    </div>
    <h2>Admin Access </h2>
    <p>Enter your administrator credentials to access the Medi Core management panel.</p>

    <div id="login-error">Incorrect username or password. Please try again.</div>

    <div class="form-group">
      <label class="form-label">Username</label>
      <input type="text" id="login-user" class="form-control" placeholder="admin" autocomplete="off">
    </div>
    <div class="form-group">
      <label class="form-label">Password</label>
      <input type="password" id="login-pass" class="form-control" placeholder="••••••••">
    </div>
    <button class="btn btn-primary btn-full" onclick="doLogin()">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
      Unlock Dashboard
    </button>
  </div>
</div>

<div id="admin-app">

  <div class="topbar">
    <div class="topbar-logo">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
      Medi <span>Core</span> &nbsp;<span style="font-weight:400;color:var(--text-muted);font-size:13px;">Admin Panel</span>
    </div>
    <div class="topbar-right">
      <div class="admin-badge">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Admin: <span id="top-username">admin</span>
      </div>
      <button class="btn-logout" onclick="doLogout()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Logout
      </button>
    </div>
  </div>

  <div class="app-body">

    <aside class="sidebar">
      <div class="sidebar-label">Main</div>
      <div class="nav-item active" data-panel="overview" onclick="showPanel('overview',this)">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Overview
      </div>

      <div class="sidebar-label">Doctors</div>
      <div class="nav-item" data-panel="add-doctor" onclick="showPanel('add-doctor',this)">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
        Add Clinician
      </div>
      <div class="nav-item" data-panel="doctors" onclick="showPanel('doctors',this); loadDoctors()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Clinicians List
      </div>

      <div class="sidebar-label">Bookings</div>
      <div class="nav-item" data-panel="bookings" onclick="showPanel('bookings',this); loadBookings()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Booking Register
      </div>

      <div class="sidebar-label" style="margin-top:auto;"></div>
      <div class="nav-item danger" style="margin-top:24px;" onclick="doLogout()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Lock Dashboard
      </div>
    </aside>

    <main class="main-content">

      <div class="panel active" id="panel-overview">
        <div class="page-title">Overview Metrics</div>
        <div class="page-sub">Real-time summary of your Medi Core system data from the database.</div>

        <div class="metrics-row">
          <div class="metric-card">
            <div class="metric-icon blue">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            <div class="metric-info">
              <h4>Total Earnings</h4>
              <div class="metric-val" id="ov-earnings">—</div>
            </div>
          </div>
          <div class="metric-card">
            <div class="metric-icon green">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="metric-info">
              <h4>Total Bookings</h4>
              <div class="metric-val" id="ov-bookings">—</div>
            </div>
          </div>
          <div class="metric-card">
            <div class="metric-icon purple">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </div>
            <div class="metric-info">
              <h4>Clinicians</h4>
              <div class="metric-val" id="ov-doctors">—</div>
            </div>
          </div>
          <div class="metric-card">
            <div class="metric-icon amber">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="metric-info">
              <h4>Patients</h4>
              <div class="metric-val" id="ov-patients">—</div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Quick Actions
          </div>
          <p style="color:var(--text-secondary);font-size:14px;margin-bottom:14px;">Manage clinicians, view bookings, and track patient appointments from one place.</p>
          <div class="quick-actions">
            <button class="btn btn-primary" onclick="showPanel('add-doctor', document.querySelector('[data-panel=add-doctor]'))">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Add New Doctor
            </button>
            <button class="btn btn-secondary" onclick="showPanel('bookings', document.querySelector('[data-panel=bookings]')); loadBookings()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              View Bookings
            </button>
            <button class="btn btn-secondary" onclick="showPanel('doctors', document.querySelector('[data-panel=doctors]')); loadDoctors()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
              Clinicians List
            </button>
          </div>
        </div>
      </div>

      <div class="panel" id="panel-add-doctor">
        <div class="page-title">Add New Clinician</div>
        <div class="page-sub">Register a new doctor into the Medi Core database.</div>

        <div class="card">
          <div class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
            Clinician Registration Form
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Full Name</label>
              <input type="text" id="f-name" class="form-control" placeholder="Dr. Sarah Jenkins">
            </div>
            <div class="form-group">
              <label class="form-label">Specialty</label>
              <select id="f-specialty" class="form-control">
                <option value="" disabled selected>Select specialty…</option>
                <option>General Physician</option>
                <option>Cardiologist</option>
                <option>Pediatrician</option>
                <option>Dermatologist</option>
                <option>Psychiatrist</option>
                <option>Neurologist</option>
                <option>Orthopedic Surgeon</option>
                <option>Gynecologist</option>
                <option>ENT Specialist</option>
                <option>Ophthalmologist</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Experience (Years)</label>
              <input type="number" id="f-exp" class="form-control" placeholder="e.g. 12" min="1" max="60">
            </div>
            <div class="form-group">
              <label class="form-label">Rating (1.0 – 5.0)</label>
              <input type="number" id="f-rating" class="form-control" placeholder="e.g. 4.8" step="0.1" min="1" max="5">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">In-Person Fee (LKR)</label>
              <input type="number" id="f-fee-ip" class="form-control" placeholder="e.g. 2500" min="0">
            </div>
            <div class="form-group">
              <label class="form-label">Virtual Fee (LKR)</label>
              <input type="number" id="f-fee-vt" class="form-control" placeholder="e.g. 1500" min="0">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Avatar Index (0–7)</label>
            <input type="number" id="f-avatar" class="form-control" placeholder="0" min="0" max="7" value="0">
            <small style="color:var(--text-muted);font-size:12px;">Corresponds to avatar index used in the main app.</small>
          </div>

          <div class="form-group">
            <label class="form-label">Professional Bio</label>
            <textarea id="f-bio" class="form-control" rows="3" placeholder="Brief background, training, and specialties…"></textarea>
          </div>

          <div class="form-actions">
            <button class="btn btn-primary" onclick="submitAddDoctor()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Register Clinician
            </button>
          </div>
        </div>
      </div>

      <div class="panel" id="panel-doctors">
        <div class="page-title">Registered Clinicians</div>
        <div class="page-sub">All doctors currently registered in the Medi Core database.</div>

        <div class="card">
          <div class="card-title" style="justify-content:space-between;">
            <span>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--secondary)" stroke-width="2" style="margin-right:6px;vertical-align:middle;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
              Clinicians Table
            </span>
            <button class="btn btn-secondary" style="font-size:12px;padding:6px 12px;" onclick="loadDoctors()">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
              Refresh
            </button>
          </div>

          <div class="table-wrapper">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Clinician</th>
                  <th>Specialty</th>
                  <th>Experience</th>
                  <th>In-Person Fee</th>
                  <th>Virtual Fee</th>
                  <th>Rating</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="tbl-doctors">
                <tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:40px;">Loading…</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="panel" id="panel-bookings">
        <div class="page-title">Booking Register</div>
        <div class="page-sub">All patient consultation bookings recorded in the system.</div>

        <div class="card">
          <div class="card-title" style="justify-content:space-between;">
            <span>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" style="margin-right:6px;vertical-align:middle;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              Consultation Invoices
            </span>
            <button class="btn btn-secondary" style="font-size:12px;padding:6px 12px;" onclick="loadBookings()">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
              Refresh
            </button>
          </div>

          <div class="table-wrapper">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Patient</th>
                  <th>Clinician</th>
                  <th>Date</th>
                  <th>Time Slot</th>
                  <th>Type</th>
                  <th>Amount</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="tbl-bookings">
                <tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:40px;">Loading…</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </main>
  </div>
</div>

<div class="modal-overlay" id="edit-modal">
  <div class="modal-box">
    <div class="modal-title">
      Edit Clinician
      <button class="modal-close" onclick="closeEditModal()">✕</button>
    </div>

    <input type="hidden" id="edit-id">

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Full Name</label>
        <input type="text" id="edit-name" class="form-control">
      </div>
      <div class="form-group">
        <label class="form-label">Specialty</label>
        <select id="edit-specialty" class="form-control">
          <option>General Physician</option>
          <option>Cardiologist</option>
          <option>Pediatrician</option>
          <option>Dermatologist</option>
          <option>Psychiatrist</option>
          <option>Neurologist</option>
          <option>Orthopedic Surgeon</option>
          <option>Gynecologist</option>
          <option>ENT Specialist</option>
          <option>Ophthalmologist</option>
        </select>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Experience (Yrs)</label>
        <input type="number" id="edit-exp" class="form-control">
      </div>
      <div class="form-group">
        <label class="form-label">Rating</label>
        <input type="number" id="edit-rating" class="form-control" step="0.1" min="1" max="5">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">In-Person Fee (LKR)</label>
        <input type="number" id="edit-fee-ip" class="form-control">
      </div>
      <div class="form-group">
        <label class="form-label">Virtual Fee (LKR)</label>
        <input type="number" id="edit-fee-vt" class="form-control">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Bio</label>
      <textarea id="edit-bio" class="form-control" rows="3"></textarea>
    </div>

    <div class="form-actions">
      <button class="btn btn-secondary" onclick="closeEditModal()" style="margin-right:8px;">Cancel</button>
      <button class="btn btn-primary" onclick="submitEditDoctor()">Save Changes</button>
    </div>
  </div>
</div>

<div id="toast"></div>

<script>
const ADMIN_USER = "admin";
const ADMIN_PASS = "admin123";

document.addEventListener("keydown", e => {
  if (e.key === "Enter" && document.getElementById("admin-app").style.display !== "flex") {
    doLogin();
  }
});

function doLogin() {
  const u = document.getElementById("login-user").value.trim();
  const p = document.getElementById("login-pass").value;
  const err = document.getElementById("login-error");

  if (u.toLowerCase() === ADMIN_USER && p === ADMIN_PASS) {
    document.getElementById("login-screen").style.display = "none";
    const app = document.getElementById("admin-app");
    app.style.display = "flex";
    document.getElementById("top-username").textContent = u;
    loadOverview();
  } else {
    err.style.display = "block";
    document.getElementById("login-pass").value = "";
  }
}

function doLogout() {
  document.getElementById("admin-app").style.display = "none";
  document.getElementById("login-screen").style.display = "flex";
  document.getElementById("login-user").value = "";
  document.getElementById("login-pass").value = "";
  document.getElementById("login-error").style.display = "none";
}

function showPanel(id, navEl) {
  document.querySelectorAll(".panel").forEach(p => p.classList.remove("active"));
  document.getElementById("panel-" + id).classList.add("active");

  document.querySelectorAll(".nav-item").forEach(n => n.classList.remove("active"));
  if (navEl) navEl.classList.add("active");
}

let toastTimer;
function showToast(msg, type = "success") {
  const t = document.getElementById("toast");
  t.textContent = msg;
  t.className = "show " + type;
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => { t.className = ""; }, 3000);
}

const API = "backend/";

async function apiFetch(endpoint, options = {}) {
  const res = await fetch(API + endpoint, options);
  if (!res.ok) throw new Error("HTTP " + res.status);
  return res.json();
}

async function loadOverview() {
  try {
    const [doctors, bookings] = await Promise.all([
      apiFetch("doctors.php"),
      apiFetch("bookings.php")
    ]);

    const earnings = bookings.reduce((s, b) => s + parseFloat(b.amount || 0), 0);

    const uniquePatients = new Set(bookings.map(b => b.patient_id)).size;

    document.getElementById("ov-earnings").textContent = "LKR " + earnings.toFixed(2);
    document.getElementById("ov-bookings").textContent = bookings.length;
    document.getElementById("ov-doctors").textContent  = doctors.length;
    document.getElementById("ov-patients").textContent = uniquePatients;

  } catch (e) {
    showToast("Failed to load overview: " + e.message, "error");
  }
}

async function loadDoctors() {
  const tbody = document.getElementById("tbl-doctors");
  tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:40px;"><div class="loader"></div></td></tr>`;

  try {
    const data = await apiFetch("doctors.php");

    if (!data.length) {
      tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        <div>No clinicians registered yet.</div>
      </div></td></tr>`;
      return;
    }

    // FIX: Store doctors globally so openEditModal can safely fetch by id (avoids quote-breaking in onclick)
    window._doctorsCache = data;

    tbody.innerHTML = data.map(d => `
      <tr>
        <td style="color:var(--text-muted);font-size:12px;">#MED-${100 + parseInt(d.id)}</td>
        <td>
          <div class="doc-cell">
            <div class="doc-avatar">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
              <div class="doc-name">${esc(d.name)}</div>
              <div class="doc-id">ID: ${d.id}</div>
            </div>
          </div>
        </td>
        <td><span class="badge badge-blue">${esc(d.specialty)}</span></td>
        <td>${d.experience} yrs</td>
        <td>LKR ${parseFloat(d.fee_in_person).toLocaleString()}</td>
        <td>LKR ${parseFloat(d.fee_virtual).toLocaleString()}</td>
        <td><span class="rating-star">★ ${parseFloat(d.rating).toFixed(1)}</span></td>
        <td>
          <button class="action-btn" title="Edit" onclick="openEditModal(${d.id})">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4z"/></svg>
          </button>
          <button class="action-btn del" title="Delete" onclick="deleteDoctor(${d.id})">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
          </button>
        </td>
      </tr>
    `).join("");

  } catch (e) {
    tbody.innerHTML = `<tr><td colspan="8" style="color:var(--danger);text-align:center;padding:30px;">Error: ${e.message}</td></tr>`;
  }
}

async function submitAddDoctor() {
  const name       = document.getElementById("f-name").value.trim();
  const specialty  = document.getElementById("f-specialty").value;
  const experience = parseInt(document.getElementById("f-exp").value);
  const rating     = parseFloat(document.getElementById("f-rating").value);
  const feeIP      = parseInt(document.getElementById("f-fee-ip").value);
  const feeVT      = parseInt(document.getElementById("f-fee-vt").value);
  const avatar     = parseInt(document.getElementById("f-avatar").value) || 0;
  const bio        = document.getElementById("f-bio").value.trim();

  if (!name || !specialty || isNaN(experience) || isNaN(rating) || isNaN(feeIP) || isNaN(feeVT)) {
    showToast("Please fill in all required fields.", "error");
    return;
  }

  try {
    const data = await apiFetch("admin.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ name, specialty, experience, rating,
                             fee_in_person: feeIP, fee_virtual: feeVT,
                             avatar, bio })
    });

    if (data.status === "success") {
      showToast("Clinician " + name + " registered successfully!", "success");
      ["f-name","f-exp","f-rating","f-fee-ip","f-fee-vt","f-bio"].forEach(id => {
        document.getElementById(id).value = "";
      });
      document.getElementById("f-specialty").value = "";
      document.getElementById("f-avatar").value = "0";
      loadOverview();
    } else {
      showToast("Error: " + (data.message || "Unknown error"), "error");
    }
  } catch (e) {
    showToast("Network error: " + e.message, "error");
  }
}

async function deleteDoctor(id) {
  if (!confirm("Remove this clinician from the system? This cannot be undone.")) return;

  try {
    const data = await apiFetch("admin.php?id=" + id, { method: "DELETE" });
    showToast("Clinician removed.", "info");
    loadDoctors();
    loadOverview();
  } catch (e) {
    showToast("Delete failed: " + e.message, "error");
  }
}

// FIX: Fetch doctor from cache by id - safe from quote-injection bugs
function openEditModal(id) {
  const d = (window._doctorsCache || []).find(doc => parseInt(doc.id) === parseInt(id));
  if (!d) { showToast("Doctor not found.", "error"); return; }
  document.getElementById("edit-id").value        = d.id;
  document.getElementById("edit-name").value      = d.name;
  document.getElementById("edit-specialty").value = d.specialty;
  document.getElementById("edit-exp").value       = d.experience;
  document.getElementById("edit-rating").value    = d.rating;
  document.getElementById("edit-fee-ip").value    = d.fee_in_person;
  document.getElementById("edit-fee-vt").value    = d.fee_virtual;
  document.getElementById("edit-bio").value       = d.bio || "";
  document.getElementById("edit-modal").classList.add("active");
}

function closeEditModal() {
  document.getElementById("edit-modal").classList.remove("active");
}

async function submitEditDoctor() {
  const id        = document.getElementById("edit-id").value;
  const name      = document.getElementById("edit-name").value.trim();
  const specialty = document.getElementById("edit-specialty").value;
  const experience = parseInt(document.getElementById("edit-exp").value);
  const rating    = parseFloat(document.getElementById("edit-rating").value);
  const feeIP     = parseInt(document.getElementById("edit-fee-ip").value);
  const feeVT     = parseInt(document.getElementById("edit-fee-vt").value);
  const bio       = document.getElementById("edit-bio").value.trim();

  if (!name || !specialty) {
    showToast("Name and specialty are required.", "error");
    return;
  }

  try {
    const data = await apiFetch("admin.php?id=" + id, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ name, specialty, experience, rating,
                             fee_in_person: feeIP, fee_virtual: feeVT, bio })
    });

    if (data.status === "success") {
      showToast("Clinician updated successfully!", "success");
      closeEditModal();
      loadDoctors();
      loadOverview();
    } else {
      showToast("Error: " + (data.message || "Unknown"), "error");
    }
  } catch (e) {
    showToast("Update failed: " + e.message, "error");
  }
}

async function loadBookings() {
  const tbody = document.getElementById("tbl-bookings");
  tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:40px;"><div class="loader"></div></td></tr>`;

  try {
    const data = await apiFetch("bookings.php");

    if (!data.length) {
      tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <div>No bookings registered yet.</div>
      </div></td></tr>`;
      return;
    }

    tbody.innerHTML = data.map(b => {
      const type = (b.consult_type || "").toLowerCase() === "virtual" ? "virtual" : "inperson";
      const badgeClass = type === "virtual" ? "badge-purple" : "badge-green";
      const badgeLabel = type === "virtual" ? "Virtual" : "In-Person";
      return `
        <tr>
          <td style="color:var(--text-muted);font-size:12px;">#BK-${b.id}</td>
          <td>
            <div style="font-weight:600;">${esc(b.patient_name || "—")}</div>
            <div style="font-size:11px;color:var(--text-muted);">ID: ${b.patient_id}</div>
          </td>
          <td>${esc(b.doctor_name || "—")}</td>
          <td>${esc(b.booking_date || "—")}</td>
          <td style="color:var(--primary);">${esc(b.time_slot || "—")}</td>
          <td><span class="badge ${badgeClass}">${badgeLabel}</span></td>
          <td style="font-weight:700;color:var(--accent);">LKR ${parseFloat(b.amount || 0).toFixed(2)}</td>
          <td>
            <button class="action-btn del" title="Cancel Booking" onclick="deleteBooking(${b.id})">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
            </button>
          </td>
        </tr>
      `;
    }).join("");

  } catch (e) {
    tbody.innerHTML = `<tr><td colspan="8" style="color:var(--danger);text-align:center;padding:30px;">Error: ${e.message}</td></tr>`;
  }
}

async function deleteBooking(id) {
  if (!confirm("Cancel and delete this booking? This cannot be undone.")) return;

  try {
    // FIX: Call admin.php with booking_id param - bookings.php had no DELETE handler
    const data = await apiFetch("admin.php?action=deleteBooking&id=" + id, { method: "DELETE" });
    showToast("Booking deleted.", "info");
    loadBookings();
    loadOverview();
  } catch (e) {
    showToast("Delete failed: " + e.message, "error");
  }
}

function esc(str) {
  return String(str)
    .replace(/&/g,"&amp;")
    .replace(/</g,"&lt;")
    .replace(/>/g,"&gt;")
    .replace(/"/g,"&quot;")
    .replace(/'/g,"&#39;");
}
</script>

</body>
</html>
