<?php
require_once __DIR__ . '/../../includes/auth_check.php';

// Cek ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setAlert('danger', 'ID barang tidak ditemukan!');
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];

// Ambil data barang
$query = mysqli_query($conn, "SELECT * FROM barang WHERE id_barang = $id");
if (mysqli_num_rows($query) == 0) {
    setAlert('danger', 'Data barang tidak ditemukan!');
    header("Location: index.php");
    exit();
}
$barang = mysqli_fetch_assoc($query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    if (empty($_POST['nama_barang']) || empty($_POST['stok_barang']) || empty($_POST['harga_satuan'])) {
        setAlert('danger', 'Semua field harus diisi!');
    } else {
        // Bersihkan input
        $nama_barang = bersihkanInput($_POST['nama_barang']);
        $varian_barang = bersihkanInput($_POST['varian_barang']);
        $id_kategori = !empty($_POST['id_kategori']) ? (int)$_POST['id_kategori'] : 'NULL';
        $stok_barang = (int)$_POST['stok_barang'];
        $harga_satuan = (int)$_POST['harga_satuan'];
        
        // Validasi stok tidak boleh minus
        if ($stok_barang < 0) {
            setAlert('danger', 'Stok tidak boleh minus!');
        } else {
            // Proses upload foto baru jika ada
            $foto = $barang['foto']; // pakai foto lama
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                // Upload foto baru
                $upload = uploadFoto($_FILES['foto'], $barang['kode_barang']);
                if ($upload['status']) {
                    // Hapus foto lama
                    hapusFoto($barang['foto']);
                    $foto = $upload['nama_file'];
                } else {
                    setAlert('danger', $upload['pesan']);
                    header("Location: edit.php?id=$id");
                    exit();
                }
            }
            
            // Hapus foto jika checkbox dicentang
            if (isset($_POST['hapus_foto']) && $_POST['hapus_foto'] == 1) {
                hapusFoto($barang['foto']);
                $foto = 'NULL';
            }
            
            $query = "UPDATE barang SET 
                      nama_barang = '$nama_barang',
                      varian_barang = '$varian_barang',
                      id_kategori = $id_kategori,
                      foto = " . ($foto == 'NULL' ? 'NULL' : "'$foto'") . ",
                      stok_barang = $stok_barang,
                      harga_satuan = $harga_satuan
                      WHERE id_barang = $id";
            
            if (mysqli_query($conn, $query)) {
                $detail = "Mengedit barang: {$nama_barang} ({$barang['kode_barang']}) - Stok: {$stok_barang}, Harga: Rp " . number_format($harga_satuan, 0, ',', '.');
                catatLog($conn, 'Edit Barang', 'barang', $id, $detail);
                
                setAlert('success', 'Data barang berhasil diupdate!');
                header("Location: index.php");
                exit();
            } else {
                setAlert('danger', 'Gagal mengupdate data: ' . mysqli_error($conn));
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
            <i class="fas fa-edit"></i>
            <div>
                <h4>Edit Barang</h4>
                <p>Edit data barang</p>
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

    <!-- Form Edit -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-box me-2"></i>Form Edit Barang</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Kode Barang</label>
                            <input type="text" class="form-control" value="<?php echo $barang['kode_barang']; ?>" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="nama_barang" class="form-control" value="<?php echo $barang['nama_barang']; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Varian</label>
                            <input type="text" name="varian_barang" class="form-control" value="<?php echo $barang['varian_barang']; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="id_kategori" class="form-control">
                                <option value="">-- Pilih Kategori --</option>
                                <?php 
                                mysqli_data_seek($kategori, 0);
                                while($k = mysqli_fetch_assoc($kategori)): 
                                    $selected = ($k['id_kategori'] == $barang['id_kategori']) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $k['id_kategori']; ?>" <?php echo $selected; ?>>
                                    <?php echo $k['nama_kategori']; ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Stok <span class="text-danger">*</span></label>
                            <input type="number" name="stok_barang" class="form-control" value="<?php echo $barang['stok_barang']; ?>" min="0" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Harga Satuan <span class="text-danger">*</span></label>
                            <input type="number" name="harga_satuan" class="form-control" value="<?php echo $barang['harga_satuan']; ?>" min="0" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Foto Barang</label>
                            <?php if (!empty($barang['foto'])): ?>
                                <div class="mb-2">
                                    <img src="http://ukk-esther_.test/uploads/barang/<?php echo $barang['foto']; ?>" 
                                         style="max-width: 100px; max-height: 100px; border: 1px solid #ddd; border-radius: 5px; padding: 5px;">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="hapus_foto" id="hapus_foto" value="1">
                                        <label class="form-check-label text-danger" for="hapus_foto">
                                            <i class="fas fa-trash me-1"></i>Hapus foto
                                        </label>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="foto" class="form-control" accept="image/jpeg,image/png,image/jpg,image/gif">
                            <small class="text-muted">Format: JPG, JPEG, PNG, GIF. Maks: 2MB</small>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update
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