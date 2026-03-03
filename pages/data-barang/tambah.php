<?php
require_once __DIR__ . '/../../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    if (empty($_POST['nama_barang']) || empty($_POST['stok_barang']) || empty($_POST['harga_satuan'])) {
        setAlert('danger', 'Semua field harus diisi!');
    } else {
        // Bersihkan input
        $kode_barang = generateKodeBarang($conn);
        $nama_barang = bersihkanInput($_POST['nama_barang']);
        $varian_barang = bersihkanInput($_POST['varian_barang']);
        $stok_barang = (int)$_POST['stok_barang'];
        $harga_satuan = (int)$_POST['harga_satuan'];
        
        // Validasi stok tidak boleh minus
        if ($stok_barang < 0) {
            setAlert('danger', 'Stok tidak boleh minus!');
        } else {
            $query = "INSERT INTO barang (kode_barang, nama_barang, varian_barang, stok_barang, harga_satuan) 
                      VALUES ('$kode_barang', '$nama_barang', '$varian_barang', $stok_barang, $harga_satuan)";
            
            if (mysqli_query($conn, $query)) {
                setAlert('success', 'Data barang berhasil ditambahkan!');
                header("Location: index.php");
                exit();
            } else {
                setAlert('danger', 'Gagal menambahkan data: ' . mysqli_error($conn));
            }
        }
    }
}
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<!-- Content -->
<div id="content">
    <!-- Navbar Top -->
    <div class="navbar-top">
        <div class="page-title">
            <i class="fas fa-plus-circle"></i>
            <div>
                <h4>Tambah Barang</h4>
                <p>Tambah data barang baru</p>
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

    <!-- Form Tambah -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-box me-2"></i>Form Tambah Barang</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                    <input type="text" name="nama_barang" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Varian</label>
                    <input type="text" name="varian_barang" class="form-control" placeholder="Contoh: Merk, Tipe, dll">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Stok <span class="text-danger">*</span></label>
                    <input type="number" name="stok_barang" class="form-control" min="0" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Harga Satuan <span class="text-danger">*</span></label>
                    <input type="number" name="harga_satuan" class="form-control" min="0" required>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>    