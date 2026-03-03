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
            <i class="fas fa-chart-bar"></i>
            <div>
                <h4>Laporan</h4>
                <p>Cetak laporan transaksi</p>
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

    <!-- Form Filter Laporan -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-filter me-2"></i>Filter Laporan</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="cetak.php" target="_blank">
                        <div class="mb-3">
                            <label class="form-label">Jenis Laporan</label>
                            <select name="jenis" class="form-control">
                                <option value="semua">Semua Transaksi</option>
                                <option value="masuk">Barang Masuk</option>
                                <option value="keluar">Barang Keluar</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Periode</label>
                            <select name="periode" class="form-control" id="periode">
                                <option value="hari">Hari Ini</option>
                                <option value="minggu">Minggu Ini</option>
                                <option value="bulan">Bulan Ini</option>
                                <option value="tahun">Tahun Ini</option>
                                <option value="custom">Kustom</option>
                            </select>
                        </div>
                        
                        <div class="row" id="tanggal_custom" style="display: none;">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="tanggal_akhir" class="form-control">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-print me-2"></i>Cetak Laporan
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle me-2"></i>Informasi</h5>
                </div>
                <div class="card-body">
                    <p>Laporan dapat dicetak berdasarkan:</p>
                    <ul>
                        <li>Semua transaksi</li>
                        <li>Barang masuk saja</li>
                        <li>Barang keluar saja</li>
                        <li>Periode tertentu (harian, mingguan, bulanan, tahunan, atau kustom)</li>
                    </ul>
                    <p class="text-muted mb-0">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>
                        Laporan akan dibuka di tab baru dan siap untuk dicetak.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Tampilkan input tanggal kustom jika memilih periode custom
    document.getElementById('periode').addEventListener('change', function() {
        if (this.value === 'custom') {
            document.getElementById('tanggal_custom').style.display = 'flex';
        } else {
            document.getElementById('tanggal_custom').style.display = 'none';
        }
    });
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>