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
            <i class="fas fa-cubes"></i>
            <div>
                <h4>Data Barang</h4>
                <p>Kelola data barang inventaris</p>
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

    <!-- Tombol Tambah - Hanya untuk ADMIN -->
    <?php if ($_SESSION['level'] == 'admin'): ?>
    <div class="mb-4">
        <a href="tambah.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Barang
        </a>
    </div>
    <?php endif; ?>

    <!-- Tabel Data Barang -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-list me-2"></i>Daftar Barang</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Varian</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Harga Satuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($conn, 
                            "SELECT b.*, k.nama_kategori 
                             FROM barang b 
                             LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
                             ORDER BY b.id_barang DESC"
                        );
                        $no = 1;
                        while($row = mysqli_fetch_assoc($query)):
                            $stok_class = ($row['stok_barang'] <= 5) ? 'text-danger fw-bold' : '';
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <?php if (!empty($row['foto'])): ?>
                                    <img src="<?php echo $base_url; ?>uploads/barang/<?php echo $row['foto']; ?>" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd;"
                                         alt="Foto Barang">
                                <?php else: ?>
                                    <div style="width: 50px; height: 50px; background: #f8f9fc; border-radius: 5px; display: flex; align-items: center; justify-content: center; border: 1px dashed #ccc;">
                                        <i class="fas fa-image text-muted" style="font-size: 1.2rem;"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $row['kode_barang']; ?></td>
                            <td><?php echo $row['nama_barang']; ?></td>
                            <td><?php echo $row['varian_barang'] ?: '-'; ?></td>
                            <td><?php echo $row['nama_kategori'] ?: '-'; ?></td>
                            <td class="<?php echo $stok_class; ?>"><?php echo $row['stok_barang']; ?></td>
                            <td>Rp <?php echo number_format($row['harga_satuan'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if ($_SESSION['level'] == 'admin'): ?>
                                    <!-- Admin: Bisa edit dan hapus -->
                                    <a href="edit.php?id=<?php echo $row['id_barang']; ?>" class="btn-action btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="hapus.php?id=<?php echo $row['id_barang']; ?>" 
                                       class="btn-action btn-delete" 
                                       title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php else: ?>
                                    <!-- Staff: Hanya bisa lihat -->
                                    <span class="text-muted"><i class="fas fa-eye"></i> Read Only</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>