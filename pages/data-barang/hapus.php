<?php
require_once '../../includes/auth_check.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (empty($id)) {
    $_SESSION['alert'] = [
        'tipe' => 'danger',
        'pesan' => 'ID barang tidak ditemukan!'
    ];
    header("Location: index.php");
    exit();
}

// Ambil data barang untuk ditampilkan
$query = "SELECT * FROM barang WHERE id_barang = $id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    $_SESSION['alert'] = [
        'tipe' => 'danger',
        'pesan' => 'Data barang tidak ditemukan!'
    ];
    header("Location: index.php");
    exit();
}

$barang = mysqli_fetch_assoc($result);

// Ambil data kategori
$query_kategori = "SELECT nama_kategori FROM kategori WHERE id_kategori = {$barang['id_kategori']}";
$result_kategori = mysqli_query($conn, $query_kategori);
$kategori = mysqli_fetch_assoc($result_kategori);

// Cek apakah barang digunakan di transaksi
$cek_transaksi = mysqli_query($conn, "SELECT id_transaksi FROM transaksi WHERE id_barang = $id LIMIT 1");
$barang_dipakai = (mysqli_num_rows($cek_transaksi) > 0);

// Proses penghapusan jika user mengkonfirmasi
if (isset($_POST['confirm']) && $_POST['confirm'] == 'yes') {
    
    // Cek ulang apakah barang digunakan di transaksi
    $cek_lagi = mysqli_query($conn, "SELECT id_transaksi FROM transaksi WHERE id_barang = $id LIMIT 1");
    if (mysqli_num_rows($cek_lagi) > 0) {
        $error = "Barang tidak dapat dihapus karena sudah memiliki transaksi!";
    } else {
        // Hapus foto terlebih dahulu jika ada
        if (!empty($barang['foto'])) {
            hapusFoto($barang['foto']);
        }
        
        // Hapus data barang
        $delete = "DELETE FROM barang WHERE id_barang = $id";
        
        if (mysqli_query($conn, $delete)) {
            $detail = "Menghapus barang: {$barang['nama_barang']} ({$barang['kode_barang']})";
            catatLog($conn, 'Hapus Barang', 'barang', $id, $detail);
            
            $_SESSION['alert'] = [
                'tipe' => 'success',
                'pesan' => 'Data barang berhasil dihapus!'
            ];
            header("Location: index.php");
            exit();
        } else {
            $error = "Gagal menghapus data: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Barang - Sistem Inventaris</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .confirm-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            max-width: 550px;
            width: 100%;
            text-align: center;
        }
        
        .confirm-icon {
            width: 80px;
            height: 80px;
            background: #ffebee;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .confirm-icon i {
            font-size: 40px;
            color: #e74a3b;
        }
        
        .confirm-icon.warning {
            background: #fff3e0;
        }
        
        .confirm-icon.warning i {
            color: #f6c23e;
        }
        
        .confirm-card h3 {
            color: #e74a3b;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .confirm-card p {
            color: #666;
            margin-bottom: 10px;
        }
        
        .barang-info {
            background: #f8f9fc;
            border-radius: 16px;
            padding: 25px;
            margin: 25px 0;
            text-align: left;
            border: 1px solid #eaecf4;
        }
        
        .barang-info .foto-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            border-bottom: none;
            padding-bottom: 0;
        }
        
        .barang-info .foto-preview {
            width: 120px;
            height: 120px;
            border-radius: 10px;
            border: 2px solid #4e73df;
            padding: 5px;
            background: white;
        }
        
        .barang-info .foto-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .barang-info .foto-preview .no-foto {
            width: 100%;
            height: 100%;
            background: #f8f9fc;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ccc;
            font-size: 2rem;
        }
        
        .barang-info div {
            margin: 12px 0;
            display: flex;
            align-items: center;
            border-bottom: 1px dashed #eaecf4;
            padding-bottom: 8px;
        }
        
        .barang-info div:last-child {
            border-bottom: none;
        }
        
        .barang-info i {
            width: 30px;
            font-size: 1.2rem;
            color: #4e73df;
        }
        
        .barang-info strong {
            width: 100px;
            color: #4e73df;
            font-weight: 600;
        }
        
        .barang-info span {
            flex: 1;
            color: #5a5c69;
            font-weight: 500;
        }
        
        .stok-badge {
            background: <?php echo ($barang['stok_barang'] <= 5) ? '#ffebee' : '#e8f5e9'; ?>;
            color: <?php echo ($barang['stok_barang'] <= 5) ? '#e74a3b' : '#1cc88a'; ?>;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
            margin-left: 10px;
        }
        
        .btn-hapus {
            background: #e74a3b;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
            border: none;
        }
        
        .btn-hapus:hover {
            background: #c62828;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 74, 59, 0.3);
            color: white;
        }
        
        .btn-batal {
            background: #f8f9fc;
            color: #5a5c69;
            border: 2px solid #eaecf4;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
            margin-left: 10px;
            display: inline-block;
        }
        
        .btn-batal:hover {
            background: #eaecf4;
            color: #4e73df;
            transform: translateY(-2px);
        }
        
        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border: none;
            border-left: 5px solid;
            text-align: left;
        }
        
        .alert-danger {
            background: #ffebee;
            color: #e74a3b;
            border-left-color: #e74a3b;
        }
        
        .alert-warning {
            background: #fff3e0;
            color: #f6c23e;
            border-left-color: #f6c23e;
        }
        
        .alert i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="confirm-card">
        <?php if ($barang_dipakai): ?>
            <div class="confirm-icon warning">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            
            <h3 style="color: #f6c23e;">Tidak Dapat Dihapus</h3>
            <p class="mb-4">Barang ini tidak dapat dihapus karena sudah memiliki transaksi.</p>
            
            <div class="barang-info">
                <div class="foto-container">
                    <div class="foto-preview">
                        <?php if (!empty($barang['foto'])): ?>
                            <img src="http://ukk-esther_.test/uploads/barang/<?php echo $barang['foto']; ?>" alt="Foto Barang">
                        <?php else: ?>
                            <div class="no-foto">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div>
                    <i class="fas fa-barcode"></i>
                    <strong>Kode:</strong>
                    <span><?php echo $barang['kode_barang']; ?></span>
                </div>
                <div>
                    <i class="fas fa-box"></i>
                    <strong>Nama:</strong>
                    <span><?php echo $barang['nama_barang']; ?></span>
                </div>
                <div>
                    <i class="fas fa-tag"></i>
                    <strong>Varian:</strong>
                    <span><?php echo $barang['varian_barang'] ?: '-'; ?></span>
                </div>
                <div>
                    <i class="fas fa-tags"></i>
                    <strong>Kategori:</strong>
                    <span><?php echo $kategori['nama_kategori'] ?: '-'; ?></span>
                </div>
                <div>
                    <i class="fas fa-cubes"></i>
                    <strong>Stok:</strong>
                    <span>
                        <?php echo $barang['stok_barang']; ?>
                        <span class="stok-badge"><?php echo ($barang['stok_barang'] <= 5) ? 'Menipis' : 'Aman'; ?></span>
                    </span>
                </div>
                <div>
                    <i class="fas fa-money-bill"></i>
                    <strong>Harga:</strong>
                    <span>Rp <?php echo number_format($barang['harga_satuan'], 0, ',', '.'); ?></span>
                </div>
            </div>
            
            <div class="alert alert-warning">
                <i class="fas fa-info-circle"></i>
                Barang yang sudah memiliki transaksi tidak dapat dihapus untuk menjaga integritas data.
            </div>
            
            <a href="index.php" class="btn-batal">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            
        <?php else: ?>
            <div class="confirm-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            
            <h3>Hapus Data Barang?</h3>
            <p>Anda yakin ingin menghapus data barang berikut?</p>
            
            <?php if (isset($error) && $error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="barang-info">
                <div class="foto-container">
                    <div class="foto-preview">
                        <?php if (!empty($barang['foto'])): ?>
                            <img src="http://ukk-esther_.test/uploads/barang/<?php echo $barang['foto']; ?>" alt="Foto Barang">
                        <?php else: ?>
                            <div class="no-foto">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div>
                    <i class="fas fa-barcode"></i>
                    <strong>Kode:</strong>
                    <span><?php echo $barang['kode_barang']; ?></span>
                </div>
                <div>
                    <i class="fas fa-box"></i>
                    <strong>Nama:</strong>
                    <span><?php echo $barang['nama_barang']; ?></span>
                </div>
                <div>
                    <i class="fas fa-tag"></i>
                    <strong>Varian:</strong>
                    <span><?php echo $barang['varian_barang'] ?: '-'; ?></span>
                </div>
                <div>
                    <i class="fas fa-tags"></i>
                    <strong>Kategori:</strong>
                    <span><?php echo $kategori['nama_kategori'] ?: '-'; ?></span>
                </div>
                <div>
                    <i class="fas fa-cubes"></i>
                    <strong>Stok:</strong>
                    <span>
                        <?php echo $barang['stok_barang']; ?>
                        <span class="stok-badge"><?php echo ($barang['stok_barang'] <= 5) ? 'Menipis' : 'Aman'; ?></span>
                    </span>
                </div>
                <div>
                    <i class="fas fa-money-bill"></i>
                    <strong>Harga:</strong>
                    <span>Rp <?php echo number_format($barang['harga_satuan'], 0, ',', '.'); ?></span>
                </div>
            </div>
            
            <p class="text-danger mb-4">
                <i class="fas fa-info-circle me-2"></i>
                Data yang sudah dihapus tidak dapat dikembalikan!
            </p>
            
            <form method="POST" action="">
                <input type="hidden" name="confirm" value="yes">
                <button type="submit" class="btn-hapus">
                    <i class="fas fa-trash me-2"></i>Ya, Hapus
                </button>
                <a href="index.php" class="btn-batal">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>