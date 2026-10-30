<?php
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Maju Jaya | Accounting Information System</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
<script>window.APP_NAME="MAJU JAYA";</script>
</head>
<body>
<div class="app-shell">
<aside class="sidebar" id="sidebar">
 <div class="brand"><div class="brand-mark">MJ</div><div class="brand-copy"><strong>MAJU JAYA</strong><span>Accounting System</span></div><button class="sidebar-close" id="sidebarClose" aria-label="Tutup menu">×</button></div>
 <div class="profile-card"><div class="avatar">A</div><div class="profile-info"><strong>Administrator</strong><span>System Admin</span></div><button class="more-btn" aria-label="Account options">•••</button></div>
 <div class="nav-label">WORKSPACE</div>
 <nav class="navigation">
  <button class="nav-item" data-page="coa"><span class="nav-icon">▤</span><span>Chart of Accounts</span></button>
  <button class="nav-item active" data-page="dashboard"><span class="nav-icon">▦</span><span>Dashboard</span></button>
  <button class="nav-item has-sub" id="masterToggle"><span class="nav-icon">▣</span><span>Master Data</span><span class="chevron" id="masterChevron">⌄</span></button>
  <div class="submenu open" id="masterSub">
   <button class="sub-item" data-page="barang"><i></i>Product Data</button>
   <button class="sub-item" data-page="pelanggan"><i></i>Customer Data</button>
   <button class="sub-item" data-page="pemasok"><i></i>Supplier Data</button>
   <button class="sub-item" data-page="karyawan"><i></i>Employee Data</button>
  </div>
  <button class="nav-item" data-page="reports"><span class="nav-icon">▥</span><span>Financial Reports</span></button>
  <button class="nav-item" data-page="settings"><span class="nav-icon">⚙</span><span>Settings</span></button>
 </nav>
 <div class="sidebar-bottom"><div class="help-card"><div class="help-icon">✦</div><div><b>Need help?</b><span>Contact system support</span></div><span class="help-arrow">→</span></div><div class="sidebar-foot">© 2026 Maju Jaya<br><span>Accounting workspace</span></div></div>
</aside>
<div class="mobile-scrim" id="scrim"></div>
<main class="main-area">
 <header class="topbar"><div class="top-left"><button class="menu-toggle" id="menuToggle" aria-label="Buka menu">☰</button><div><div class="breadcrumb">Workspace <span>/</span> <b id="breadcrumbText">Dashboard</b></div><h1 id="pageTitle">Dashboard</h1></div></div><div class="top-actions"><button class="icon-button" title="Notifications" aria-label="Notifikasi">♧<i></i></button><div class="top-profile"><div class="avatar small">A</div><div><b>Administrator</b><span>Admin account</span></div><span class="top-chevron">⌄</span></div></div></header>
 <section class="page-content" id="pageContent"><div class="loading-state">Loading your accounting workspace…</div></section>
 <footer class="page-footer"><span>© 2026 Fotokopi & ATK Maju Jaya</span><span>Accounting Information System <b>•</b> SAK EMKM</span></footer>
</main>
</div>
<div class="modal-layer" id="modalLayer"><div class="modal" id="modal"><div class="modal-head"><div><span class="modal-kicker">MAJU JAYA WORKSPACE</span><h2 id="modalTitle">Add new data</h2></div><button class="modal-x" id="modalClose">×</button></div><div id="modalBody"></div></div></div>
<div class="toast" id="toast"></div>
<script src="assets/app.js"></script>
</body></html>
