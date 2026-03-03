<?php
require_once __DIR__ . '/../includes/auth_check.php';

// Ambil data statistik
$total_barang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM barang"))['total'];
$total_stok = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(stok_barang) as total FROM barang"))['total'];
$barang_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(jumlah), 0) as total FROM transaksi WHERE status='masuk' AND DATE(tanggal_transaksi) = CURDATE()"))['total'];
$barang_keluar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(jumlah), 0) as total FROM transaksi WHERE status='keluar' AND DATE(tanggal_transaksi) = CURDATE()"))['total'];
$transaksi_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE DATE(tanggal_transaksi) = CURDATE()"))['total'];
$stok_menipis = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM barang WHERE stok_barang <= 5"))['total'];
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<!-- Content -->
<div id="content">
    <!-- Navbar Top -->
    <div class="navbar-top">
        <div class="page-title">
            <i class="fas fa-chart-pie"></i>
            <div>
                <h4>Dashboard</h4>
                <p>Selamat datang, <?php echo $_SESSION['nama_lengkap']; ?> (<?php echo $_SESSION['level']; ?>)!</p>
            </div>
        </div>
        <div class="user-info">
            <i class="fas fa-user-circle"></i>
            <span><?php echo $_SESSION['nama_lengkap']; ?></span>
            <span style="color: #999; font-size: 0.8rem;"><?php echo date('d M Y'); ?></span>
        </div>
    </div>
    
    <!-- Statistik Cards -->
    <div class="stats-grid">
        <!-- Total Barang -->
        <div class="stat-card">
            <div class="stat-icon" style="background: #e8f5e9;">
                <i class="fas fa-cubes" style="color: #1cc88a;"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $total_barang; ?></h3>
                <p>Total Barang</p>
            </div>
            <div class="stat-change positive">
                <i class="fas fa-box"></i> Seluruh barang terdaftar
            </div>
        </div>
        
        <!-- Total Stok -->
        <div class="stat-card">
            <div class="stat-icon" style="background: #fff3e0;">
                <i class="fas fa-database" style="color: #f6c23e;"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $total_stok ?: 0; ?></h3>
                <p>Total Stok</p>
            </div>
            <div class="stat-change positive">
                <i class="fas fa-layer-group"></i> Semua stok barang
            </div>
        </div>
        
        <!-- Barang Masuk -->
        <div class="stat-card">
            <div class="stat-icon" style="background: #e3f2fd;">
                <i class="fas fa-arrow-down" style="color: #36b9cc;"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $barang_masuk; ?></h3>
                <p>Barang Masuk</p>
            </div>
            <div class="stat-change">
                <i class="fas fa-clock"></i> <?php echo date('d M Y'); ?>
            </div>
        </div>
        
        <!-- Barang Keluar -->
        <div class="stat-card">
            <div class="stat-icon" style="background: #ffebee;">
                <i class="fas fa-arrow-up" style="color: #e74a3b;"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $barang_keluar; ?></h3>
                <p>Barang Keluar</p>
            </div>
            <div class="stat-change">
                <i class="fas fa-clock"></i> <?php echo date('d M Y'); ?>
            </div>
        </div>
    </div>
    
    <!-- Row untuk Chart dan Informasi -->
    <div class="row">
        <!-- Chart Statistik -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-chart-pie me-2"></i>Statistik Transaksi Hari Ini</h5>
                    <span style="color: #666; font-size: 0.9rem;"><?php echo date('d F Y'); ?></span>
                </div>
                <div class="card-body">
                    <div style="display: flex; gap: 20px; align-items: center;">
                        <div style="flex: 1;">
                            <canvas id="chartTransaksi" style="max-height: 200px;"></canvas>
                        </div>
                        <div style="flex: 1;">
                            <div style="background: #e8f5e9; padding: 15px; border-radius: 10px; margin-bottom: 10px;">
                                <div style="font-size: 0.9rem; color: #666;">Barang Masuk</div>
                                <div style="font-size: 1.8rem; font-weight: 700; color: #1cc88a;"><?php echo $barang_masuk; ?></div>
                            </div>
                            <div style="background: #ffebee; padding: 15px; border-radius: 10px;">
                                <div style="font-size: 0.9rem; color: #666;">Barang Keluar</div>
                                <div style="font-size: 1.8rem; font-weight: 700; color: #e74a3b;"><?php echo $barang_keluar; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Informasi -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle me-2"></i>Informasi</h5>
                </div>
                <div class="card-body">
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                            <i class="fas fa-box" style="font-size: 2.5rem; color: #4e73df;"></i>
                            <div>
                                <h6 style="margin: 0; font-weight: 600;">Sistem Inventaris Gudang</h6>
                                <p style="margin: 0; color: #666; font-size: 0.9rem;">Manajemen Stok Barang</p>
                            </div>
                        </div>
                        
                        <div style="background: #f8f9fc; padding: 15px; border-radius: 10px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span><i class="fas fa-check-circle text-success me-2"></i>Total Barang</span>
                                <span class="fw-bold"><?php echo $total_barang; ?> item</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span><i class="fas fa-check-circle text-success me-2"></i>Total Stok</span>
                                <span class="fw-bold"><?php echo $total_stok ?: 0; ?> unit</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span><i class="fas fa-check-circle text-success me-2"></i>Transaksi Hari Ini</span>
                                <span class="fw-bold"><?php echo $transaksi_hari_ini; ?> transaksi</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span><i class="fas fa-exclamation-triangle text-warning me-2"></i>Stok Menipis</span>
                                <span class="fw-bold text-warning"><?php echo $stok_menipis; ?> item</span>
                            </div>
                        </div>
                    </div>
                    
                    <p style="color: #666; font-size: 0.9rem; margin: 0;">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>
                        Gunakan menu di samping untuk mengelola data barang dan transaksi.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stok Menipis -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Stok Menipis (Stok <= 5)</h5>
            <a href="data-barang/index.php">
                Lihat Semua <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Varian</th>
                            <th>Stok</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($conn, "SELECT * FROM barang WHERE stok_barang <= 5 ORDER BY stok_barang ASC");
                        if (mysqli_num_rows($query) > 0):
                            while($row = mysqli_fetch_assoc($query)):
                                $badge = ($row['stok_barang'] == 0) ? 'danger' : 'warning';
                                $status = ($row['stok_barang'] == 0) ? 'Habis' : 'Menipis';
                        ?>
                                <tr>
                                    <td><?php echo $row['kode_barang']; ?></td>
                                    <td><?php echo $row['nama_barang']; ?></td>
                                    <td><?php echo $row['varian_barang']; ?></td>
                                    <td><?php echo $row['stok_barang']; ?></td>
                                    <td>
                                        <span class="status-badge badge-<?php echo $badge; ?>">
                                            <?php echo $status; ?>
                                        </span>
                                    </td>
                                </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-check-circle me-2" style="font-size: 1.5rem; color: #1cc88a;"></i>
                                    <p class="mt-2 text-muted">Semua stok aman</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Transaksi Terbaru -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-history me-2"></i>5 Transaksi Terbaru</h5>
            <a href="transaksi/index.php">
                Lihat Semua <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>Tujuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($conn, 
                            "SELECT t.*, b.nama_barang, b.varian_barang 
                             FROM transaksi t 
                             JOIN barang b ON t.id_barang = b.id_barang 
                             ORDER BY t.tanggal_transaksi DESC LIMIT 5");
                        if (mysqli_num_rows($query) > 0):
                            while($row = mysqli_fetch_assoc($query)):
                                $badge = ($row['status'] == 'masuk') ? 'status-masuk' : 'status-keluar';
                        ?>
                                <tr>
                                    <td><?php echo date('d-m-Y H:i', strtotime($row['tanggal_transaksi'])); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $badge; ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $row['nama_barang'] . ' - ' . $row['varian_barang']; ?></td>
                                    <td><?php echo $row['jumlah']; ?></td>
                                    <td><?php echo $row['tujuan']; ?></td>
                                </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-calendar-times me-2" style="font-size: 1.5rem; color: #ccc;"></i>
                                    <p class="mt-2 text-muted">Belum ada data transaksi</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Script untuk Chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart Transaksi
    const ctx = document.getElementById('chartTransaksi').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Barang Masuk', 'Barang Keluar'],
            datasets: [{
                data: [<?php echo $barang_masuk; ?>, <?php echo $barang_keluar; ?>],
                backgroundColor: ['#1cc88a', '#e74a3b'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            cutout: '60%'
        }
    });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>