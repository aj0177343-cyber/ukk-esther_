<?php
// Ambil base_url dari header
global $base_url;

// Ambil nama file saat ini untuk menentukan menu aktif
$current_page = basename($_SERVER['PHP_SELF']);
$current_folder = basename(dirname($_SERVER['PHP_SELF']));
?>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="fas fa-box"></i>
        </div>
        <h4>Inventaris Gudang</h4>
        <p>Sistem Manajemen Stok</p>
    </div>
    
    <div class="sidebar-menu">
        <a href="<?php echo $base_url; ?>pages/dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        <a href="<?php echo $base_url; ?>pages/data-barang/index.php" class="<?php echo ($current_folder == 'data-barang') ? 'active' : ''; ?>">
            <i class="fas fa-cubes"></i>
            <span>Data Barang</span>
        </a>
        <a href="<?php echo $base_url; ?>pages/transaksi/index.php" class="<?php echo ($current_folder == 'transaksi') ? 'active' : ''; ?>">
            <i class="fas fa-exchange-alt"></i>
            <span>Transaksi</span>
        </a>
        <a href="<?php echo $base_url; ?>pages/laporan/index.php" class="<?php echo ($current_folder == 'laporan') ? 'active' : ''; ?>">
            <i class="fas fa-chart-bar"></i>
            <span>Laporan</span>
        </a>
        
        <div class="logout">
            <a href="<?php echo $base_url; ?>auth/logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
    
    <!-- Sidebar Toggle Button -->
    <button id="sidebarToggle">
        <i class="fas fa-chevron-left"></i>
    </button>
</div>

<!-- Content Wrapper -->
<div id="content-wrapper">
    <!-- Main Content -->
    <div id="content">