<?php
require_once __DIR__ . '/../../includes/auth_check.php';

// Hanya admin yang bisa akses log
if ($_SESSION['level'] != 'admin') {
    setAlert('danger', 'Anda tidak memiliki akses ke halaman ini!');
    header("Location: ../dashboard.php");
    exit();
}

// Cek apakah tabel log_aktivitas ada
$cek_tabel = mysqli_query($conn, "SHOW TABLES LIKE 'log_aktivitas'");
if (mysqli_num_rows($cek_tabel) == 0) {
    setAlert('danger', 'Tabel log_aktivitas belum dibuat! Silakan jalankan SQL terlebih dahulu.');
    header("Location: ../dashboard.php");
    exit();
}

// Filter berdasarkan parameter
$filter_user = isset($_GET['user']) ? (int)$_GET['user'] : 0;
$filter_aktivitas = isset($_GET['aktivitas']) ? $_GET['aktivitas'] : '';
$filter_tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';

// Query dasar
$query = "SELECT l.*, u.nama_lengkap FROM log_aktivitas l 
          JOIN users u ON l.id_user = u.id_user 
          WHERE 1=1";

// Tambah filter
if ($filter_user > 0) {
    $query .= " AND l.id_user = $filter_user";
}
if (!empty($filter_aktivitas)) {
    $query .= " AND l.aktivitas LIKE '%$filter_aktivitas%'";
}
if (!empty($filter_tanggal)) {
    $query .= " AND DATE(l.created_at) = '$filter_tanggal'";
}

$query .= " ORDER BY l.created_at DESC";
$log = mysqli_query($conn, $query);

// Ambil daftar user untuk filter
$users = mysqli_query($conn, "SELECT id_user, username, nama_lengkap FROM users ORDER BY nama_lengkap ASC");

// Ambil daftar aktivitas unik untuk filter
$aktivitas_list = mysqli_query($conn, "SELECT DISTINCT aktivitas FROM log_aktivitas ORDER BY aktivitas ASC");
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<!-- Content -->
<div id="content">
    <!-- Navbar Top -->
    <div class="navbar-top">
        <div class="page-title">
            <i class="fas fa-history"></i>
            <div>
                <h4>Log Aktivitas</h4>
                <p>Riwayat aktivitas pengguna</p>
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

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-filter me-2"></i>Filter Log</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">User</label>
                    <select name="user" class="form-control">
                        <option value="">Semua User</option>
                        <?php 
                        mysqli_data_seek($users, 0);
                        while($u = mysqli_fetch_assoc($users)): 
                        ?>
                        <option value="<?php echo $u['id_user']; ?>" <?php echo ($filter_user == $u['id_user']) ? 'selected' : ''; ?>>
                            <?php echo $u['nama_lengkap']; ?> (<?php echo $u['username']; ?>)
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Aktivitas</label>
                    <select name="aktivitas" class="form-control">
                        <option value="">Semua Aktivitas</option>
                        <?php 
                        if ($aktivitas_list && mysqli_num_rows($aktivitas_list) > 0):
                            while($a = mysqli_fetch_assoc($aktivitas_list)): 
                        ?>
                        <option value="<?php echo $a['aktivitas']; ?>" <?php echo ($filter_aktivitas == $a['aktivitas']) ? 'selected' : ''; ?>>
                            <?php echo $a['aktivitas']; ?>
                        </option>
                        <?php 
                            endwhile;
                        endif;
                        ?>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?php echo $filter_tanggal; ?>">
                </div>
                
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-sync-alt me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Log -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-list me-2"></i>Riwayat Aktivitas</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Level</th>
                            <th>Aktivitas</th>
                            <th>Tabel</th>
                            <th>Detail</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($log && mysqli_num_rows($log) > 0):
                            $no = 1;
                            while($row = mysqli_fetch_assoc($log)): 
                                $badge = getBadgeLog($row['aktivitas']);
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td><?php echo date('d-m-Y H:i:s', strtotime($row['created_at'])); ?></td>
                            <td>
                                <?php echo $row['nama_lengkap']; ?><br>
                                <small class="text-muted"><?php echo $row['username']; ?></small>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo ($row['level'] == 'admin') ? 'primary' : 'secondary'; ?>">
                                    <?php echo ucfirst($row['level']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $badge; ?>">
                                    <?php echo $row['aktivitas']; ?>
                                </span>
                            </td>
                            <td><?php echo $row['tabel'] ?: '-'; ?></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-info" 
                                        onclick="lihatDetail(<?php echo $row['id_log']; ?>, '<?php echo addslashes($row['detail']); ?>', '<?php echo $row['aktivitas']; ?>', '<?php echo $row['username']; ?>', '<?php echo date('d-m-Y H:i:s', strtotime($row['created_at'])); ?>')"
                                        title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                            <td><?php echo $row['ip_address']; ?></td>
                        </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-history" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
                                <p class="text-muted mb-2">Belum ada data log aktivitas</p>
                                <p class="text-muted small">Lakukan beberapa aktivitas seperti login, tambah barang, atau transaksi untuk melihat log.</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Log -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2 text-info"></i>Detail Log Aktivitas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">ID Log</th>
                        <td id="detail_id_log">-</td>
                    </tr>
                    <tr>
                        <th>Username</th>
                        <td id="detail_username">-</td>
                    </tr>
                    <tr>
                        <th>Aktivitas</th>
                        <td id="detail_aktivitas">-</td>
                    </tr>
                    <tr>
                        <th>Waktu</th>
                        <td id="detail_waktu">-</td>
                    </tr>
                    <tr>
                        <th>Detail</th>
                        <td id="detail_text" style="white-space: pre-wrap;"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Script untuk Modal Detail -->
<script>
function lihatDetail(id, detail, aktivitas, username, waktu) {
    document.getElementById('detail_id_log').textContent = id;
    document.getElementById('detail_username').textContent = username;
    document.getElementById('detail_aktivitas').textContent = aktivitas;
    document.getElementById('detail_waktu').textContent = waktu;
    document.getElementById('detail_text').textContent = detail;
    
    new bootstrap.Modal(document.getElementById('detailModal')).show();
}
</script>

<!-- Script untuk DataTable -->
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
        },
        order: [[1, 'desc']], // Urutkan berdasarkan kolom waktu (descending)
        columnDefs: [
            { orderable: false, targets: [6] } // Kolom aksi tidak bisa diurutkan
        ]
    });
});
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>