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
        $stok_barang = (int)$_POST['stok_barang'];
        $harga_satuan = (int)$_POST['harga_satuan'];
        
        // Validasi stok tidak boleh minus
        if ($stok_barang < 0) {
            setAlert('danger', 'Stok tidak boleh minus!');
        } else {
            $query = "UPDATE barang SET 
                      nama_barang = '$nama_barang',
                      varian_barang = '$varian_barang',
                      stok_barang = $stok_barang,
                      harga_satuan = $harga_satuan
                      WHERE id_barang = $id";
            
            if (mysqli_query($conn, $query)) {
                setAlert('success', 'Data barang berhasil diupdate!');
                header("Location: index.php");
                exit();
            } else {
                setAlert('danger', 'Gagal mengupdate data: ' . mysqli_error($conn));
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
            <form method="POST" action="">
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
                    <label class="form-label">Stok <span class="text-danger">*</span></label>
                    <input type="number" name="stok_barang" class="form-control" value="<?php echo $barang['stok_barang']; ?>" min="0" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Harga Satuan <span class="text-danger">*</span></label>
                    <input type="number" name="harga_satuan" class="form-control" value="<?php echo $barang['harga_satuan']; ?>" min="0" required>
                </div>
                
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