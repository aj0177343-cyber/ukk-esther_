<?php
require_once __DIR__ . '/../../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    if (empty($_POST['id_barang']) || empty($_POST['jumlah']) || empty($_POST['tujuan'])) {
        setAlert('danger', 'Semua field harus diisi!');
    } else {
        $id_barang = (int)$_POST['id_barang'];
        $jumlah = (int)$_POST['jumlah'];
        $tujuan = bersihkanInput($_POST['tujuan']);
        
        // Validasi jumlah tidak boleh 0 atau minus
        if ($jumlah <= 0) {
            setAlert('danger', 'Jumlah harus lebih dari 0!');
        } else {
            // Insert transaksi
            $query = "INSERT INTO transaksi (id_barang, status, jumlah, tujuan) 
                      VALUES ($id_barang, 'masuk', $jumlah, '$tujuan')";
            
            if (mysqli_query($conn, $query)) {
                $id_transaksi = mysqli_insert_id($conn);
                // Update stok barang (tambah)
                mysqli_query($conn, "UPDATE barang SET stok_barang = stok_barang + $jumlah WHERE id_barang = $id_barang");
                
                // Ambil data barang untuk detail
                $q = mysqli_query($conn, "SELECT nama_barang, kode_barang FROM barang WHERE id_barang = $id_barang");
                $brg = mysqli_fetch_assoc($q);
                $detail = "Barang masuk: {$brg['nama_barang']} ({$brg['kode_barang']}) - Jumlah: {$jumlah}, Tujuan: {$tujuan}";
                catatLog($conn, 'Barang Masuk', 'transaksi', $id_transaksi, $detail);
                
                setAlert('success', 'Transaksi barang masuk berhasil dicatat!');
                header("Location: index.php");
                exit();
            } else {
                setAlert('danger', 'Gagal mencatat transaksi: ' . mysqli_error($conn));
            }
        }
    }
}

// Ambil daftar barang untuk dropdown
$barang = mysqli_query($conn, "SELECT * FROM barang ORDER BY nama_barang ASC");
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<!-- Content -->
<div id="content">
    <!-- Navbar Top -->
    <div class="navbar-top">
        <div class="page-title">
            <i class="fas fa-arrow-down text-success"></i>
            <div>
                <h4>Barang Masuk</h4>
                <p>Catat barang masuk ke gudang</p>
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

    <!-- Form Barang Masuk -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-box me-2"></i>Form Barang Masuk</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Pilih Barang <span class="text-danger">*</span></label>
                    <select name="id_barang" class="form-control" required>
                        <option value="">-- Pilih Barang --</option>
                        <?php while($row = mysqli_fetch_assoc($barang)): ?>
                        <option value="<?php echo $row['id_barang']; ?>">
                            <?php echo $row['kode_barang'] . ' - ' . $row['nama_barang'] . ' ' . $row['varian_barang']; ?> 
                            (Stok: <?php echo $row['stok_barang']; ?>)
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah" class="form-control" min="1" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Tujuan / Keterangan <span class="text-danger">*</span></label>
                    <textarea name="tujuan" class="form-control" rows="3" placeholder="Contoh: Restock dari supplier, Pengadaan baru, dll" required></textarea>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Simpan Transaksi
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