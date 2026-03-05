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
        $id_kategori = !empty($_POST['id_kategori']) ? (int)$_POST['id_kategori'] : 'NULL';
        $stok_barang = (int)$_POST['stok_barang'];
        $harga_satuan = (int)$_POST['harga_satuan'];
        
        // Validasi stok tidak boleh minus
        if ($stok_barang < 0) {
            setAlert('danger', 'Stok tidak boleh minus!');
        } else {
            // Upload foto jika ada
            $foto = 'NULL';
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $upload = uploadFoto($_FILES['foto'], $kode_barang);
                if ($upload['status']) {
                    $foto = "'" . $upload['nama_file'] . "'";
                } else {
                    setAlert('danger', $upload['pesan']);
                    header("Location: tambah.php");
                    exit();
                }
            }
            
            $query = "INSERT INTO barang (kode_barang, nama_barang, varian_barang, id_kategori, foto, stok_barang, harga_satuan) 
                      VALUES ('$kode_barang', '$nama_barang', '$varian_barang', $id_kategori, $foto, $stok_barang, $harga_satuan)";
            
            if (mysqli_query($conn, $query)) {
                $id_barang = mysqli_insert_id($conn);
                $detail = "Menambah barang: {$nama_barang} ({$kode_barang}) - Stok: {$stok_barang}, Harga: Rp " . number_format($harga_satuan, 0, ',', '.');
                catatLog($conn, 'Tambah Barang', 'barang', $id_barang, $detail);
                
                setAlert('success', 'Data barang berhasil ditambahkan!');
                header("Location: index.php");
                exit();
            } else {
                setAlert('danger', 'Gagal menambahkan data: ' . mysqli_error($conn));
            }
        }
    }
}

// Ambil daftar kategori
$kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
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
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="nama_barang" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Varian</label>
                            <input type="text" name="varian_barang" class="form-control" placeholder="Contoh: Merk, Tipe, dll">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="id_kategori" class="form-control">
                                <option value="">-- Pilih Kategori --</option>
                                <?php while($k = mysqli_fetch_assoc($kategori)): ?>
                                <option value="<?php echo $k['id_kategori']; ?>"><?php echo $k['nama_kategori']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Stok <span class="text-danger">*</span></label>
                            <input type="number" name="stok_barang" class="form-control" min="0" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Harga Satuan <span class="text-danger">*</span></label>
                            <input type="number" name="harga_satuan" class="form-control" min="0" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Foto Barang</label>
                            <input type="file" name="foto" class="form-control" accept="image/jpeg,image/png,image/jpg,image/gif">
                            <small class="text-muted">Format: JPG, JPEG, PNG, GIF. Maks: 2MB</small>
                        </div>
                    </div>
                </div>
                
                <hr>
                
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