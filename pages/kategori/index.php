<?php
require_once __DIR__ . '/../../includes/auth_check.php';

// Hanya admin yang bisa akses kategori
if ($_SESSION['level'] != 'admin') {
    setAlert('danger', 'Anda tidak memiliki akses ke halaman ini!');
    header("Location: ../dashboard.php");
    exit();
}

// Proses tambah kategori
if (isset($_POST['tambah'])) {
    $nama = bersihkanInput($_POST['nama_kategori']);
    $deskripsi = bersihkanInput($_POST['deskripsi']);
    
    if (empty($nama)) {
        setAlert('danger', 'Nama kategori harus diisi!');
    } else {
        $query = "INSERT INTO kategori (nama_kategori, deskripsi) VALUES ('$nama', '$deskripsi')";
        if (mysqli_query($conn, $query)) {
            // Catat log
            $id_kategori = mysqli_insert_id($conn);
            $detail = "Menambah kategori: {$nama}";
            catatLog($conn, 'Tambah Kategori', 'kategori', $id_kategori, $detail);
            
            setAlert('success', 'Kategori berhasil ditambahkan!');
        } else {
            setAlert('danger', 'Gagal menambahkan kategori: ' . mysqli_error($conn));
        }
    }
    header("Location: index.php");
    exit();
}

// Proses edit kategori
if (isset($_POST['edit'])) {
    $id = (int)$_POST['id_kategori'];
    $nama = bersihkanInput($_POST['nama_kategori']);
    $deskripsi = bersihkanInput($_POST['deskripsi']);
    
    // Ambil data lama untuk log
    $query_lama = mysqli_query($conn, "SELECT * FROM kategori WHERE id_kategori = $id");
    $lama = mysqli_fetch_assoc($query_lama);
    
    $query = "UPDATE kategori SET nama_kategori='$nama', deskripsi='$deskripsi' WHERE id_kategori=$id";
    if (mysqli_query($conn, $query)) {
        // Catat log
        $detail = "Mengedit kategori: {$lama['nama_kategori']} -> {$nama}";
        catatLog($conn, 'Edit Kategori', 'kategori', $id, $detail);
        
        setAlert('success', 'Kategori berhasil diupdate!');
    } else {
        setAlert('danger', 'Gagal mengupdate kategori: ' . mysqli_error($conn));
    }
    header("Location: index.php");
    exit();
}

// Proses hapus kategori
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    
    // Cek apakah kategori masih digunakan
    $cek = mysqli_query($conn, "SELECT id_barang, nama_barang FROM barang WHERE id_kategori = $id");
    if (mysqli_num_rows($cek) > 0) {
        $barang = mysqli_fetch_assoc($cek);
        setAlert('danger', "Kategori tidak dapat dihapus karena masih digunakan oleh barang: {$barang['nama_barang']}");
    } else {
        // Ambil data kategori untuk log
        $query_kategori = mysqli_query($conn, "SELECT * FROM kategori WHERE id_kategori = $id");
        $kategori_hapus = mysqli_fetch_assoc($query_kategori);
        
        $query = "DELETE FROM kategori WHERE id_kategori=$id";
        if (mysqli_query($conn, $query)) {
            // Catat log
            $detail = "Menghapus kategori: {$kategori_hapus['nama_kategori']}";
            catatLog($conn, 'Hapus Kategori', 'kategori', $id, $detail);
            
            setAlert('success', 'Kategori berhasil dihapus!');
        } else {
            setAlert('danger', 'Gagal menghapus kategori: ' . mysqli_error($conn));
        }
    }
    header("Location: index.php");
    exit();
}

// Ambil data kategori
$kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<!-- Content -->
<div id="content">
    <!-- Navbar Top -->
    <div class="navbar-top">
        <div class="page-title">
            <i class="fas fa-tags"></i>
            <div>
                <h4>Kategori Barang</h4>
                <p>Kelola kategori barang inventaris</p>
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

    <div class="row">
        <!-- Form Tambah Kategori -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-plus-circle me-2"></i>Tambah Kategori</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Elektronik, Komputer, Office" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Opsional"></textarea>
                        </div>
                        <button type="submit" name="tambah" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Daftar Kategori -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-list me-2"></i>Daftar Kategori</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kategori</th>
                                    <th>Deskripsi</th>
                                    <th>Jumlah Barang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if (mysqli_num_rows($kategori) > 0):
                                    $no = 1;
                                    while($row = mysqli_fetch_assoc($kategori)): 
                                        $jml = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM barang WHERE id_kategori = {$row['id_kategori']}"));
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $no++; ?></td>
                                    <td><?php echo $row['nama_kategori']; ?></td>
                                    <td><?php echo $row['deskripsi'] ?: '-'; ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-info"><?php echo $jml['total']; ?></span>
                                    </td>
                                    <td>
                                        <button class="btn-action btn-edit" onclick="editKategori(<?php echo $row['id_kategori']; ?>, '<?php echo $row['nama_kategori']; ?>', '<?php echo $row['deskripsi']; ?>')" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="?hapus=<?php echo $row['id_kategori']; ?>" 
                                           class="btn-action btn-delete" 
                                           title="Hapus"
                                           onclick="return confirm('Yakin ingin menghapus kategori <?php echo $row['nama_kategori']; ?>?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="fas fa-tags" style="font-size: 3rem; color: #ccc; margin-bottom: 10px;"></i>
                                        <p class="text-muted">Belum ada data kategori</p>
                                        <p class="text-muted small">Klik tombol "Tambah Kategori" untuk menambah data.</p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2 text-warning"></i>Edit Kategori
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_kategori" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kategori" id="edit_nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="edit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script untuk Modal Edit -->
<script>
function editKategori(id, nama, deskripsi) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_deskripsi').value = deskripsi;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>

<!-- Script untuk DataTable -->
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
        },
        columnDefs: [
            { orderable: false, targets: [4] } // Kolom aksi tidak bisa diurutkan
        ]
    });
});
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>