<?php
require_once __DIR__ . '/../../includes/auth_check.php';

// Ambil parameter filter
$jenis = isset($_GET['jenis']) ? $_GET['jenis'] : 'semua';
$periode = isset($_GET['periode']) ? $_GET['periode'] : 'hari';
$tanggal_mulai = isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : '';
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '';

// Buat kondisi query berdasarkan filter
$kondisi = "";

// Filter berdasarkan jenis
if ($jenis != 'semua') {
    $kondisi .= " AND t.status = '$jenis'";
}

// Filter berdasarkan periode
if ($periode == 'hari') {
    $kondisi .= " AND DATE(t.tanggal_transaksi) = CURDATE()";
    $judul_periode = "Hari Ini (" . date('d-m-Y') . ")";
} elseif ($periode == 'minggu') {
    $kondisi .= " AND YEARWEEK(t.tanggal_transaksi) = YEARWEEK(CURDATE())";
    $judul_periode = "Minggu Ini";
} elseif ($periode == 'bulan') {
    $kondisi .= " AND MONTH(t.tanggal_transaksi) = MONTH(CURDATE()) AND YEAR(t.tanggal_transaksi) = YEAR(CURDATE())";
    $judul_periode = "Bulan " . date('F Y');
} elseif ($periode == 'tahun') {
    $kondisi .= " AND YEAR(t.tanggal_transaksi) = YEAR(CURDATE())";
    $judul_periode = "Tahun " . date('Y');
} elseif ($periode == 'custom' && !empty($tanggal_mulai) && !empty($tanggal_akhir)) {
    $kondisi .= " AND DATE(t.tanggal_transaksi) BETWEEN '$tanggal_mulai' AND '$tanggal_akhir'";
    $judul_periode = "Periode " . date('d-m-Y', strtotime($tanggal_mulai)) . " s/d " . date('d-m-Y', strtotime($tanggal_akhir));
} else {
    $kondisi .= "";
    $judul_periode = "Semua Waktu";
}

// Judul berdasarkan jenis
if ($jenis == 'masuk') {
    $judul_jenis = "Laporan Barang Masuk";
} elseif ($jenis == 'keluar') {
    $judul_jenis = "Laporan Barang Keluar";
} else {
    $judul_jenis = "Laporan Semua Transaksi";
}

// Query ambil data
$query = "SELECT t.*, b.kode_barang, b.nama_barang, b.varian_barang 
          FROM transaksi t 
          JOIN barang b ON t.id_barang = b.id_barang 
          WHERE 1=1 $kondisi 
          ORDER BY t.tanggal_transaksi DESC";
$result = mysqli_query($conn, $query);

// Hitung total
$total_masuk = 0;
$total_keluar = 0;
$total_transaksi = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan - <?php echo $judul_jenis; ?></title>
    
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
            background: white;
            padding: 30px;
        }
        
        .header-laporan {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #4e73df;
        }
        
        .header-laporan h2 {
            color: #4e73df;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .header-laporan p {
            color: #666;
            margin-bottom: 5px;
        }
        
        .info-laporan {
            background: #f8f9fc;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            border-left: 5px solid #4e73df;
        }
        
        .info-laporan .row {
            display: flex;
            flex-wrap: wrap;
        }
        
        .info-laporan .col-md-4 {
            margin-bottom: 10px;
        }
        
        .info-laporan i {
            color: #4e73df;
            width: 25px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th {
            background: #4e73df;
            color: white;
            font-weight: 500;
            padding: 12px;
            text-align: center;
        }
        
        .table td {
            padding: 10px;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .table tbody tr:hover {
            background: #f8f9fc;
        }
        
        .badge-masuk {
            background: #e8f5e9;
            color: #1cc88a;
            padding: 5px 10px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
        }
        
        .badge-keluar {
            background: #ffebee;
            color: #e74a3b;
            padding: 5px 10px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
        }
        
        .footer-laporan {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e3e6f0;
            text-align: right;
        }
        
        .footer-laporan p {
            margin-bottom: 5px;
            color: #666;
        }
        
        .footer-laporan .tanggal {
            margin-top: 50px;
        }
        
        .btn-print {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #4e73df;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 500;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
            transition: all 0.3s;
        }
        
        .btn-print:hover {
            background: #224abe;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(78, 115, 223, 0.4);
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .fw-bold {
            font-weight: 600;
        }
        
        @media print {
            .btn-print {
                display: none;
            }
            
            body {
                padding: 15px;
            }
            
            .table th {
                background: #4e73df !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">
        <i class="fas fa-print me-2"></i>Cetak / Simpan PDF
    </button>
    
    <div class="header-laporan">
        <h2>SISTEM INVENTARIS GUDANG</h2>
        <p><?php echo $judul_jenis; ?></p>
        <p>Periode: <?php echo $judul_periode; ?></p>
    </div>
    
    <div class="info-laporan">
        <div class="row">
            <div class="col-md-4">
                <i class="fas fa-calendar"></i> 
                <strong>Tanggal Cetak:</strong> <?php echo date('d-m-Y H:i:s'); ?>
            </div>
            <div class="col-md-4">
                <i class="fas fa-list"></i> 
                <strong>Jenis:</strong> <?php echo ucfirst($jenis); ?>
            </div>
            <div class="col-md-4">
                <i class="fas fa-chart-bar"></i> 
                <strong>Total Transaksi:</strong> <?php echo $total_transaksi; ?>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-4">
                <i class="fas fa-user"></i> 
                <strong>Dicetak oleh:</strong> <?php echo $_SESSION['nama_lengkap']; ?> (<?php echo $_SESSION['level']; ?>)
            </div>
        </div>
    </div>
    
    <?php if (mysqli_num_rows($result) > 0): ?>
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Varian</th>
                <th>Status</th>
                <th>Jumlah</th>
                <th>Tujuan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while($row = mysqli_fetch_assoc($result)): 
                if ($row['status'] == 'masuk') {
                    $total_masuk += $row['jumlah'];
                } else {
                    $total_keluar += $row['jumlah'];
                }
            ?>
            <tr>
                <td class="text-center"><?php echo $no++; ?></td>
                <td><?php echo date('d-m-Y H:i', strtotime($row['tanggal_transaksi'])); ?></td>
                <td><?php echo $row['kode_barang']; ?></td>
                <td><?php echo $row['nama_barang']; ?></td>
                <td><?php echo $row['varian_barang'] ?: '-'; ?></td>
                <td class="text-center">
                    <span class="badge-<?php echo $row['status']; ?>">
                        <?php echo ucfirst($row['status']); ?>
                    </span>
                </td>
                <td class="text-center"><?php echo $row['jumlah']; ?></td>
                <td><?php echo $row['tujuan']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr style="background: #f8f9fc; font-weight: 600;">
                <td colspan="6" class="text-right">Total Barang Masuk:</td>
                <td class="text-center"><?php echo $total_masuk; ?></td>
                <td></td>
            </tr>
            <tr style="background: #f8f9fc; font-weight: 600;">
                <td colspan="6" class="text-right">Total Barang Keluar:</td>
                <td class="text-center"><?php echo $total_keluar; ?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    <?php else: ?>
    <div class="alert alert-info text-center">
        <i class="fas fa-info-circle me-2"></i>
        Tidak ada data transaksi untuk periode ini.
    </div>
    <?php endif; ?>
    
    <div class="footer-laporan">
        <p>Mengetahui,</p>
        <p>Kepala Gudang</p>
        <br><br>
        <p class="fw-bold">( ______________________ )</p>
        <p class="tanggal"><?php echo date('d-m-Y'); ?></p>
    </div>
</body>
</html>