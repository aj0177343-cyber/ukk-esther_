<?php
// Ambil nama file saat ini untuk menentukan menu aktif
$current_page = basename($_SERVER['PHP_SELF']);
$current_folder = basename(dirname($_SERVER['PHP_SELF']));
?>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <!-- Sidebar Header (Fixed) -->
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="fas fa-box"></i>
        </div>
        <h4>Inventaris Gudang</h4>
        <p>Sistem Manajemen Stok</p>
    </div>
    
    <!-- Scrollable Menu Container -->
    <div class="sidebar-menu-container">
        <div class="sidebar-menu">
            <!-- Dashboard -->
            <a href="/pages/dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            
            <!-- Data Barang -->
            <a href="/pages/data-barang/index.php" class="<?php echo ($current_folder == 'data-barang') ? 'active' : ''; ?>">
                <i class="fas fa-cubes"></i>
                <span>Data Barang</span>
            </a>
            
            <!-- Kategori - Hanya Admin -->
            <?php if ($_SESSION['level'] == 'admin'): ?>
            <a href="/pages/kategori/index.php" class="<?php echo ($current_folder == 'kategori') ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i>
                <span>Kategori</span>
            </a>
            <?php endif; ?>
            
            <!-- Transaksi -->
            <a href="/pages/transaksi/index.php" class="<?php echo ($current_folder == 'transaksi') ? 'active' : ''; ?>">
                <i class="fas fa-exchange-alt"></i>
                <span>Transaksi</span>
            </a>
            
            <!-- Laporan -->
            <a href="/pages/laporan/index.php" class="<?php echo ($current_folder == 'laporan') ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar"></i>
                <span>Laporan</span>
            </a>
            
            <!-- Log Aktivitas - Hanya Admin -->
            <?php if ($_SESSION['level'] == 'admin'): ?>
            <a href="/pages/log/index.php" class="<?php echo ($current_folder == 'log') ? 'active' : ''; ?>">
                <i class="fas fa-history"></i>
                <span>Log Aktivitas</span>
            </a>
            <?php endif; ?>
            
            <!-- Logout -->
            <div class="logout">
                <a href="/auth/logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- User Info (Fixed at Bottom) -->
    <div class="user-info-sidebar">
        <i class="fas fa-user-circle"></i>
        <span><?php echo $_SESSION['nama_lengkap']; ?> (<?php echo $_SESSION['level']; ?>)</span>
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