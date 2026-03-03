<?php
// Fungsi untuk mencegah XSS
function bersihkanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fungsi untuk cek login
function cekLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../auth/login.php");
        exit();
    }
}

// Fungsi untuk format rupiah
function rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Fungsi untuk notifikasi alert
function setAlert($tipe, $pesan) {
    $_SESSION['alert'] = [
        'tipe' => $tipe, // success, danger, warning, info
        'pesan' => $pesan
    ];
}

// Fungsi untuk menampilkan alert
function tampilAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        echo "<div class='alert alert-{$alert['tipe']} alert-dismissible fade show' role='alert'>
                <i class='fas ";
        
        // Icon berdasarkan tipe alert
        if ($alert['tipe'] == 'success') echo 'fa-check-circle';
        else if ($alert['tipe'] == 'danger') echo 'fa-exclamation-circle';
        else if ($alert['tipe'] == 'warning') echo 'fa-exclamation-triangle';
        else if ($alert['tipe'] == 'info') echo 'fa-info-circle';
        
        echo " me-2'></i>
                {$alert['pesan']}
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
        unset($_SESSION['alert']);
    }
}

// Fungsi untuk generate kode barang
function generateKodeBarang($conn) {
    $query = "SELECT MAX(id_barang) as max_id FROM barang";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'] + 1;
    return 'BRG' . str_pad($max_id, 3, '0', STR_PAD_LEFT);
}

// Fungsi untuk update stok barang
function updateStok($conn, $id_barang, $status, $jumlah) {
    if ($status == 'masuk') {
        $query = "UPDATE barang SET stok_barang = stok_barang + $jumlah WHERE id_barang = $id_barang";
    } else {
        // Cek stok cukup atau tidak
        $cek = mysqli_query($conn, "SELECT stok_barang FROM barang WHERE id_barang = $id_barang");
        $data = mysqli_fetch_assoc($cek);
        if ($data['stok_barang'] < $jumlah) {
            return false; // Stok tidak cukup
        }
        $query = "UPDATE barang SET stok_barang = stok_barang - $jumlah WHERE id_barang = $id_barang";
    }
    return mysqli_query($conn, $query);
}

// Fungsi untuk mendapatkan nama bulan dalam bahasa Indonesia
function bulanIndonesia($bulan) {
    $bulan_arr = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];
    return $bulan_arr[(int)$bulan];
}

// Fungsi untuk format tanggal Indonesia
function tanggalIndonesia($tanggal) {
    $date = date_create($tanggal);
    $bulan = bulanIndonesia(date_format($date, 'n'));
    return date_format($date, 'd') . ' ' . $bulan . ' ' . date_format($date, 'Y');
}
?>