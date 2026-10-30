<?php
require __DIR__ . '/config/auth.php';
$user = null;
$u = auth_user();
if (is_array($u)) {
    $user = [
        'username' => $u['username'],
        'nama' => $u['nama_lengkap'],
        'peran' => $u['peran'],
    ];
}
$loggedIn = is_array($user) && !empty($user['username']);
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Maju Jaya | Accounting Information System</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
<script>window.APP_NAME="MAJU JAYA";<?php if($loggedIn): ?>window.CURRENT_USER=<?php echo json_encode($user,JSON_UNESCAPED_UNICODE);?>;<?php endif; ?></script>
</head>
<body>
<?php if(!$loggedIn): ?>
<div class="login-screen" style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;background:linear-gradient(135deg,#eef4ff,#f7f9fc)">
  <div class="panel" style="width:100%;max-width:420px;padding:32px">
    <div style="text-align:center;margin-bottom:24px"><div class="brand-mark" style="margin:0 auto 12px">MJ</div><h1 style="margin:0;font-size:24px">MAJU JAYA</h1><p class="muted" style="margin:6px 0 0">Accounting Information System</p></div>
    <form id="loginForm">
      <div class="field"><label>Username</label><input name="username" id="loginUsername" required autofocus></div>
      <div class="field"><label>Password</label><input name="password" id="loginPassword" type="password" required></div>
      <div id="loginError" class="muted" style="color:#c0392b;margin:8px 0;min-height:18px"></div>
      <button class="btn btn-primary" type="submit" style="width:100%">Masuk</button>
    </form>
  </div>
</div>
<script>
document.getElementById('loginForm').addEventListener('submit', async e => {
  e.preventDefault();
  const err = document.getElementById('loginError');
  err.textContent = '';
  try {
    const res = await fetch('api.php?action=login', {method:'POST', headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'}, body:JSON.stringify({username:document.getElementById('loginUsername').value, password:document.getElementById('loginPassword').value})});
    const data = await res.json();
    if (!res.ok || data.error) throw new Error(data.error || 'Login gagal');
    location.reload();
  } catch (e2) { err.textContent = e2.message; }
});
</script>
<?php else: ?>
<div class="app-shell">
<aside class="sidebar" id="sidebar">
 <div class="brand"><div class="brand-mark">MJ</div><div class="brand-copy"><strong>MAJU JAYA</strong><span>Accounting System</span></div><button class="sidebar-close" id="sidebarClose" aria-label="Tutup menu">×</button></div>
 <div class="profile-card"><div class="avatar" id="sidebarAvatar">A</div><div class="profile-info"><strong id="sidebarName">Administrator</strong><span id="sidebarRole">System Admin</span></div><button class="logout-btn" aria-label="Account options" id="logoutBtn"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>Keluar</button></div>
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
  <button class="nav-item" data-page="journal"><span class="nav-icon">☰</span><span>Jurnal Umum</span></button>
  <button class="nav-item" data-page="reports"><span class="nav-icon">▥</span><span>Financial Reports</span></button>
  <button class="nav-item" data-page="settings"><span class="nav-icon">⚙</span><span>Settings</span></button>
 </nav>
 <div class="sidebar-bottom"><div class="help-card"><div class="help-icon">✦</div><div><b>Need help?</b><span>Contact system support</span></div><span class="help-arrow">→</span></div><div class="sidebar-foot">© 2026 Maju Jaya<br><span>Accounting workspace</span></div></div>
</aside>
<div class="mobile-scrim" id="scrim"></div>
<main class="main-area">
 <header class="topbar"><div class="top-left"><button class="menu-toggle" id="menuToggle" aria-label="Buka menu">☰</button><div><div class="breadcrumb">Workspace <span>/</span> <b id="breadcrumbText">Dashboard</b></div><h1 id="pageTitle">Dashboard</h1></div></div><div class="top-actions"><button class="icon-button" title="Notifikasi (belum tersedia)" aria-label="Notifikasi" disabled>♧<i></i></button><div class="top-profile"><div class="avatar small" id="topAvatar">A</div><div><b id="topName">Administrator</b><span id="topRole">Admin account</span></div><span class="top-chevron">⌄</span></div></div></header>
 <section class="page-content" id="pageContent"><div class="loading-state">Loading your accounting workspace…</div></section>
 <footer class="page-footer"><span>© 2026 Fotokopi & ATK Maju Jaya</span><span>Accounting Information System <b>•</b> SAK EMKM</span></footer>
</main>
</div>
<div class="modal-layer" id="modalLayer"><div class="modal" id="modal"><div class="modal-head"><div><span class="modal-kicker">MAJU JAYA WORKSPACE</span><h2 id="modalTitle">Add new data</h2></div><button class="modal-x" id="modalClose">×</button></div><div id="modalBody"></div></div></div>
<div class="toast" id="toast"></div>
<script src="assets/app.js"></script>
<?php endif; ?>
</body></html>
