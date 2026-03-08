<?php
// Tentukan base URL
$base_url = 'http://ukk-esther_.test/';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventaris Gudang</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo $base_url; ?>assets/css/custom.css" rel="stylesheet">
    
    <!-- CSS Khusus untuk Scroll Sidebar -->
    <style>
        /* ===== SIDEBAR SCROLL STYLES ===== */
        .sidebar {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar-header {
            flex-shrink: 0;
        }

        .sidebar-menu-container {
            flex: 1 1 auto;
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.3) rgba(255,255,255,0.1);
        }

        /* Custom scrollbar untuk Chrome, Edge, Safari */
        .sidebar-menu-container::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-menu-container::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }

        .sidebar-menu-container::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 10px;
        }

        .sidebar-menu-container::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }

        .user-info-sidebar {
            flex-shrink: 0;
        }

        /* Saat sidebar ditoggled */
        .sidebar.toggled .sidebar-menu-container {
            overflow-x: hidden;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar-menu-container {
                overflow-y: auto;
            }
        }
    </style>
</head>

<body>
    <div id="wrapper">