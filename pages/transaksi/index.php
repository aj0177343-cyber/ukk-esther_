<?php
require_once __DIR__ . '/../../includes/auth_check.php';
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<!-- Content -->
<div id="content">
    <!-- Navbar Top -->
    <div class="navbar-top">
        <div class="page-title">
            <i class="fas fa-exchange-alt"></i>
            <div>
                <h4>Transaksi</h4>
                <p>Daftar semua transaksi barang</p>
            </div>
        </div>
        <div class="user-info">
            <i class="fas fa-user-circle"></i>
            <span><?php echo $_SESSION['nama_lengkap']; ?></span>
            <span style="color: #999; font-size: 0.8rem;"><?php echo date('d M Y'); ?></span>
        </div>
    </div>

    <!-- Alert -->
    <?php tampilAlert(); ?>

    <!-- Tombol Tambah -->
    <div class="mb-4 d-flex gap-2">
        <a href="barang-masuk.php" class="btn btn-success">
            <i class="fas fa-arrow-down me-2"></i>Barang Masuk
        </a>
        <a href="barang-keluar.php" class="btn btn-warning">
            <i class="fas fa-arrow-up me-2"></i>Barang Keluar
        </a>
    </div>

    <!-- Tabel Transaksi -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-history me-2"></i>Riwayat Transaksi</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Barang</th>
                            <th>Varian</th>
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
                             ORDER BY t.tanggal_transaksi DESC");
                        $no = 1;
                        while($row = mysqli_fetch_assoc($query)):
                            $badge = ($row['status'] == 'masuk') ? 'status-masuk' : 'status-keluar';
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($row['tanggal_transaksi'])); ?></td>
                            <td>
                                <span class="status-badge <?php echo $badge; ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td><?php echo $row['nama_barang']; ?></td>
                            <td><?php echo $row['varian_barang'] ?: '-'; ?></td>
                            <td><?php echo $row['jumlah']; ?></td>
                            <td><?php echo $row['tujuan']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>