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
            // Cek stok cukup atau tidak
            $cek_stok = mysqli_query($conn, "SELECT stok_barang FROM barang WHERE id_barang = $id_barang");
            $stok = mysqli_fetch_assoc($cek_stok)['stok_barang'];
            
            if ($stok < $jumlah) {
                setAlert('danger', 'Stok tidak mencukupi! Stok tersedia: ' . $stok);
            } else {
                // Insert transaksi
                $query = "INSERT INTO transaksi (id_barang, status, jumlah, tujuan) 
                          VALUES ($id_barang, 'keluar', $jumlah, '$tujuan')";
                
                if (mysqli_query($conn, $query)) {
                    // Update stok barang (kurang)
                    mysqli_query($conn, "UPDATE barang SET stok_barang = stok_barang - $jumlah WHERE id_barang = $id_barang");
                    
                    setAlert('success', 'Transaksi barang keluar berhasil dicatat!');
                    header("Location: index.php");
                    exit();
                } else {
                    setAlert('danger', 'Gagal mencatat transaksi: ' . mysqli_error($conn));
                }
            }
        }
    }
}

// Ambil daftar barang untuk dropdown (hanya yang stok > 0)
$barang = mysqli_query($conn, "SELECT * FROM barang WHERE stok_barang > 0 ORDER BY nama_barang ASC");
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<!-- Content -->
<div id="content">
    <!-- Navbar Top -->
    <div class="navbar-top">
        <div class="page-title">
            <i class="fas fa-arrow-up text-warning"></i>
            <div>
                <h4>Barang Keluar</h4>
                <p>Catat barang keluar dari gudang</p>
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

    <!-- Form Barang Keluar -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-box me-2"></i>Form Barang Keluar</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Pilih Barang <span class="text-danger">*</span></label>
                    <select name="id_barang" class="form-control" required>
                        <option value="">-- Pilih Barang --</option>
                        <?php 
                        if (mysqli_num_rows($barang) > 0):
                            while($row = mysqli_fetch_assoc($barang)): 
                        ?>
                        <option value="<?php echo $row['id_barang']; ?>">
                            <?php echo $row['kode_barang'] . ' - ' . $row['nama_barang'] . ' ' . $row['varian_barang']; ?> 
                            (Stok: <?php echo $row['stok_barang']; ?>)
                        </option>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <option value="" disabled>Tidak ada barang dengan stok tersedia</option>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah" class="form-control" min="1" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Tujuan / Keterangan <span class="text-danger">*</span></label>
                    <textarea name="tujuan" class="form-control" rows="3" placeholder="Contoh: Penjualan ke customer, Pengambilan bagian IT, dll" required></textarea>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning">
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