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

// Fungsi untuk upload foto
function uploadFoto($file, $kode_barang) {
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/UKK-Esther_/uploads/barang/';
    $nama_file = $kode_barang . '_' . time() . '.' . strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $target_file = $target_dir . $nama_file;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Cek apakah file gambar
    $check = getimagesize($file['tmp_name']);
    if($check === false) {
        return ['status' => false, 'pesan' => 'File bukan gambar!'];
    }
    
    // Cek ukuran file (max 2MB)
    if ($file['size'] > 2000000) {
        return ['status' => false, 'pesan' => 'Ukuran file terlalu besar (max 2MB)!'];
    }
    
    // Format yang diperbolehkan
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        return ['status' => false, 'pesan' => 'Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan!'];
    }
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return ['status' => true, 'nama_file' => $nama_file];
    } else {
        return ['status' => false, 'pesan' => 'Gagal upload file!'];
    }
}

// Fungsi untuk hapus foto
function hapusFoto($nama_file) {
    if (!empty($nama_file)) {
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/UKK-Esther_/uploads/barang/';
        $file_path = $target_dir . $nama_file;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    return true;
}

// ============================================
// FUNGSI LOG AKTIVITAS - Tambahkan di sini
// ============================================

// Fungsi untuk mencatat log aktivitas
function catatLog($conn, $aktivitas, $tabel = null, $id_data = null, $detail = null) {
    // Cek apakah user sudah login
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    // Ambil data user dari session
    $id_user = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $level = $_SESSION['level'];
    
    // Ambil IP address user
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    // Ambil informasi browser (user agent)
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
    // Bersihkan data dari karakter khusus agar aman untuk database
    $detail = mysqli_real_escape_string($conn, $detail);
    $user_agent = mysqli_real_escape_string($conn, $user_agent);
    
    // Query untuk menyimpan log
    $query = "INSERT INTO log_aktivitas (id_user, username, level, aktivitas, tabel, id_data, detail, ip_address, user_agent) 
              VALUES ($id_user, '$username', '$level', '$aktivitas', '$tabel', $id_data, '$detail', '$ip_address', '$user_agent')";
    
    // Jalankan query dan kembalikan hasilnya
    return mysqli_query($conn, $query);
}

// Fungsi untuk mendapatkan warna badge berdasarkan aktivitas
function getBadgeLog($aktivitas) {
    // Jika aktivitas mengandung kata 'Tambah' atau 'Login'
    if (strpos($aktivitas, 'Tambah') !== false || strpos($aktivitas, 'Login') !== false) {
        return 'success'; // Warna hijau
    } 
    // Jika aktivitas mengandung kata 'Edit', 'Update', atau 'Keluar'
    elseif (strpos($aktivitas, 'Edit') !== false || strpos($aktivitas, 'Update') !== false || strpos($aktivitas, 'Keluar') !== false) {
        return 'warning'; // Warna kuning
    } 
    // Jika aktivitas mengandung kata 'Hapus'
    elseif (strpos($aktivitas, 'Hapus') !== false) {
        return 'danger'; // Warna merah
    } 
    // Jika aktivitas mengandung kata 'Logout'
    elseif (strpos($aktivitas, 'Logout') !== false) {
        return 'secondary'; // Warna abu-abu
    } 
    // Jika aktivitas mengandung kata 'Masuk'
    elseif (strpos($aktivitas, 'Masuk') !== false) {
        return 'info'; // Warna biru muda
    } 
    // Default
    else {
        return 'secondary';
    }
}
?>