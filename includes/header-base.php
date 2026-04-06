<?php

// Ensure no whitespace before this PHP tag
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();

// Include database connection if not already included
if (!isset($conn)) {
    require_once __DIR__ . '/db.php';
}

// Determine current page for active navigation
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);
$ai_host = '127.0.0.1';
$ai_port = 8000;
$connection = @fsockopen($ai_host, $ai_port, $errCode, $errStr, 1);

if (is_resource($connection)) {
    fclose($connection);
} else {
    $api_folder = "C:\\laragon\\www\\api";
    pclose(popen('start /B cmd /C "cd /d ' . $api_folder . ' && uvicorn api:app --port 8000"', 'r'));
    sleep(2);
}
$trend_log_file = __DIR__ . '/ai_trend_last_update.txt';
$today = date('Y-m-d');
$last_update = file_exists($trend_log_file) ? file_get_contents($trend_log_file) : '';

if ($last_update !== $today) {
    $ch = curl_init("http://127.0.0.1:8000/reload-trend");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_exec($ch);
    curl_close($ch);
    file_put_contents($trend_log_file, $today);
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <link rel="icon" type="image/png" href="/assets/images/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- <link rel="stylesheet" href="/assets/css/header-mobile.css"> -->

    <!-- Cart Badge CSS -->
    <link href="/assets/css/cart-badge.css" rel="stylesheet">
    <link href="/assets/css/header_base.css" rel="stylesheet">


</head>

<body>
    <!-- Two-Tier Medical Header -->
    <header class="medical-header">
        <!-- Top Bar -->
        <!-- <div class="top-bar">
            <div class="container-fluid top-bar-content">
                <div class="top-bar-left">
                    <a href="#" class="top-bar-item">
                        <i class="fas fa-search"></i>
                        <span>Tra thuốc chính hãng</span>
                        <strong>Kiểm tra ngay</strong>
                    </a>
                    <a href="tel:18006928" class="top-bar-item">
                        <i class="fas fa-phone"></i>
                        <span>Tư vấn ngay: Dalziel Đẹp Trai</span>
                    </a>
                </div>
                <div class="top-bar-right">
                    <a href="#" class="download-app">
                        <i class="fas fa-mobile-alt"></i>
                        <span>Tải ứng dụng</span>
                    </a>
                </div>
            </div>
        </div> -->

        <!-- Main Header -->
        <div class="main-header">
            <div class="container-fluid main-header-content">
                <!-- Mobile Toggle (Left) -->
                <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle menu">
                    <i class="fas fa-bars mobile-menu-icon"></i>
                </button>

                <!-- Logo Section (Center) -->
                <a href="/index.php" class="logo-section">
                    <img src="/assets/images/logo.png" alt="VetCare Store" class="logo-image">
                    <div class="logo-text">
                        <h1 class="logo-name">VetCare Store</h1>
                        <p class="logo-subtitle">Pet Clinic & Pet Care</p>
                    </div>
                </a>

                <!-- Desktop Search Bar -->
                <div class="search-bar">
                    <form action="/search.php" method="GET" class="search-input-group">
                        <input type="text" name="q" class="search-input" placeholder="Mua sản phẩm thú cưng, thuốc thú y, phụ kiện và dịch vụ chăm sóc tại VetCare Store. Uy tín - chất lượng - giá tốt." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <!-- Action Area (Right) -->
                <div class="action-area">
                    <!-- Search Toggle for Mobile -->
                    <button class="search-toggle-btn" title="Tìm kiếm" onclick="toggleMobileSearch()">
                        <i class="fas fa-search"></i>
                    </button>

                    <!-- Cart -->
                    <a href="/cart.php" class="action-icon cart-icon" title="Giỏ hàng">
                        <i class="fas fa-shopping-cart"></i>
                        <?php
                        $cart_count = 0;
                        if (isset($_SESSION['user_id']) && isset($conn)) {
                            try {
                                $cart_sql = "
                                    SELECT COUNT(DISTINCT oi.product_id) as total_quantity
                                    FROM order_items oi
                                    JOIN orders o ON oi.order_id = o.order_id
                                    WHERE o.user_id = ? AND o.status = 'cart'
                                ";
                                $cart_stmt = $conn->prepare($cart_sql);
                                if ($cart_stmt) {
                                    $cart_stmt->bind_param('i', $_SESSION['user_id']);
                                    $cart_stmt->execute();
                                    $cart_result = $cart_stmt->get_result()->fetch_assoc();
                                    $cart_count = $cart_result['total_quantity'] ?? 0;
                                }
                            } catch (Exception $e) {
                                $cart_count = 0;
                            }
                        }
                        ?>
                        <?php if ($cart_count > 0): ?>
                            <span class="cart-count"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>

                    <!-- User Account (Desktop Only) -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="custom-dropdown">
                            <button class="custom-dropdown-btn" onclick="toggleUserMenu(event)">
                                <div class="user-avatar">
                                    <?= strtoupper(substr($_SESSION['full_name'] ?? $_SESSION['username'], 0, 1)) ?>
                                </div>
                                <div class="user-info d-none d-md-block">
                                    <div class="user-name">
                                        <?= htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']) ?>
                                    </div>
                                    <div class="user-role">
                                        <?php
                                        $role_names = ["Admin" => 'Quản trị viên', "Patient" => 'Khách hàng', "Doctor" => 'Bác sĩ'];
                                        echo $role_names[$_SESSION['role_name']] ?? 'Người dùng';
                                        ?>
                                    </div>
                                </div>
                            </button>
                            <div class="custom-dropdown-menu" id="userMenu">
                                <div class="menu-item">
                                    <a href="/profile.php">
                                        <i class="fas fa-user"></i>
                                        <span>Hồ sơ cá nhân</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a href="/appointments.php">
                                        <i class="fas fa-calendar-check"></i>
                                        <span>Lịch hẹn</span>
                                    </a>
                                </div>
                                <!-- <div class="menu-item">
                                <a href="/medical-records.php">
                                    <i class="fas fa-file-medical"></i>
                                    <span>Hồ sơ bệnh án</span>
                                </a>
                            </div> -->
                                <div class="menu-item">
                                    <a href="/orders.php">
                                        <i class="fas fa-shopping-bag"></i>
                                        <span>Đơn hàng</span>
                                    </a>
                                </div>
                                <?php
                                $is_admin = false;
                                if (isset($_SESSION['role_name'])) {
                                    if ($_SESSION['role_name'] === 'admin' || $_SESSION['role_name'] === 'Admin') {
                                        $is_admin = true;
                                    }
                                } elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                                    $is_admin = true;
                                } elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                                    $is_admin = true;
                                }
                                if ($is_admin): ?>
                                    <div class="menu-divider"></div>
                                    <div class="menu-item admin-menu-item">
                                        <a href="/admin/dashboard.php">
                                            <i class="fas fa-cogs"></i>
                                            <span>Quản trị</span>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <div class="menu-divider"></div>
                                <div class="menu-item logout">
                                    <a href="#" onclick="event.stopPropagation(); showLogoutModal(); return false;">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Đăng xuất</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="/login.php" class="auth-btn login-btn">
                            <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                        </a>
                    <?php endif; ?>

                    <!-- Appointment Button (Desktop Only) -->
                    <button type="button" class="auth-btn appointment-btn d-none d-lg-flex" onclick="openAppointmentModal()">
                        <i class="fas fa-calendar-plus me-2"></i>Đặt lịch cho thú cưng
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Search Overlay -->
        <div class="mobile-search-overlay" id="mobileSearchOverlay">
            <div class="mobile-search-container">
                <button class="mobile-search-close" onclick="closeMobileSearch()">
                    <i class="fas fa-times"></i>
                </button>
                <h3 class="text-center mb-3">Tìm kiếm</h3>
                <form action="/search.php" method="GET" class="mobile-search-form">
                    <input type="text" name="q" class="mobile-search-input" placeholder="Nhập từ khóa tìm kiếm..." autocomplete="off" id="mobileSearchInput">
                    <button type="submit" class="mobile-search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Mobile Side Menu -->
        <div class="mobile-side-menu" id="mobileSideMenu">
            <div class="mobile-menu-header">
                <div class="mobile-menu-logo">
                    <img src="/assets/images/default-avatar.png" alt="VetCare Store" class="mobile-logo-img">
                    <div class="mobile-logo-text">
                        <h3>VetCare Store</h3>
                        <p>Pet Clinic & Pet Care</p>
                    </div>
                </div>
                <button class="mobile-menu-close" id="mobileMenuClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- User Section -->
            <div class="mobile-user-section">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="mobile-user-info">
                        <div class="mobile-user-avatar">
                            <?= strtoupper(substr($_SESSION['full_name'] ?? $_SESSION['username'], 0, 1)) ?>
                        </div>
                        <div class="mobile-user-details">
                            <div class="mobile-user-name">
                                <?= htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']) ?>
                            </div>
                            <div class="mobile-user-role">
                                <?php
                                $role_names = ["Admin" => 'Quản trị viên', "Patient" => 'Khách hàng', "Doctor" => 'Bác sĩ'];
                                echo $role_names[$_SESSION['role_name']] ?? 'Người dùng';
                                ?>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="mobile-auth-section">
                        <a href="/login.php" class="mobile-login-btn">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Đăng nhập</span>
                        </a>
                        <a href="/register.php" class="mobile-register-btn">
                            <i class="fas fa-user-plus"></i>
                            <span>Đăng ký</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Navigation Menu -->
            <div class="mobile-nav-section">
                <div class="mobile-nav-item">
                    <a href="/index.php" class="mobile-nav-link <?= ($current_page == 'index') ? 'active' : '' ?>">
                        <i class="fas fa-home"></i>
                        <span>Trang chủ</span>
                    </a>
                </div>

                <div class="mobile-nav-item has-submenu">
                    <a href="#" class="mobile-nav-link mobile-dropdown-toggle">
                        <i class="fas fa-paw"></i>
                        <span>Dịch vụ thú cưng</span>
                        <i class="fas fa-chevron-down mobile-nav-arrow"></i>
                    </a>

                    <div class="mobile-submenu">
                        <a href="/services.php" class="mobile-submenu-link">
                            <i class="fas fa-list"></i>
                            <span>Tất cả dịch vụ</span>
                        </a>

                        <a href="/services.php" class="mobile-submenu-link">
                            <i class="fas fa-stethoscope"></i>
                            <span>Khám & điều trị</span>
                        </a>

                        <a href="/services.php" class="mobile-submenu-link">
                            <i class="fas fa-syringe"></i>
                            <span>Tiêm phòng</span>
                        </a>

                        <a href="/services.php" class="mobile-submenu-link">
                            <i class="fas fa-cut"></i>
                            <span>Spa & grooming</span>
                        </a>

                        <a href="/services.php" class="mobile-submenu-link">
                            <i class="fas fa-heart"></i>
                            <span>Chăm sóc & tư vấn</span>
                        </a>
                    </div>
                </div>
                <div class="mobile-nav-item has-submenu">

                    <div class="mobile-submenu">
                        <a href="/doctors.php" class="mobile-submenu-link">
                            <i class="fas fa-users"></i>
                            <span>Tất cả bác sĩ</span>
                        </a>
                        <a href="/book-appointment.php" class="mobile-submenu-link">
                            <i class="fas fa-calendar-plus"></i>
                            <span>Đặt lịch khám</span>
                        </a>
                    </div>
                </div>

                <div class="mobile-nav-item has-submenu">
                    <a href="#" class="mobile-nav-link mobile-dropdown-toggle">
                        <i class="fas fa-store"></i>
                        <span>Cửa hàng</span>
                        <i class="fas fa-chevron-down mobile-nav-arrow"></i>
                    </a>
                    <div class="mobile-submenu">
                        <a href="/shop.php" class="mobile-submenu-link">
                            <i class="fas fa-pills"></i>
                            <span>Tất cả sản phẩm</span>
                        </a>
                        <a href="/shop/products.php?category=1" class="mobile-submenu-link">
                            <i class="fas fa-capsules"></i>
                            <span>Thuốc</span>
                        </a>
                        <a href="/shop/products.php?category=2" class="mobile-submenu-link">
                            <i class="fas fa-leaf"></i>
                            <span>Thực phẩm chức năng</span>
                        </a>
                        <a href="/shop/products.php?category=3" class="mobile-submenu-link">
                            <i class="fas fa-heartbeat"></i>
                            <span>Thiết bị y tế</span>
                        </a>
                        <a href="/shop/products.php?category=4" class="mobile-submenu-link">
                            <i class="fas fa-heartbeat"></i>
                            <span>Vật tư thú y</span>
                        </a>
                        <a href="/orders.php" class="mobile-submenu-link">
                            <i class="fas fa-shopping-bag"></i>
                            <span>Đơn hàng của tôi</span>
                        </a>
                    </div>
                </div>

                <div class="mobile-nav-item">
                    <a href="/about.php" class="mobile-nav-link <?= ($current_page == 'about') ? 'active' : '' ?>">
                        <i class="fas fa-info-circle"></i>
                        <span>Giới thiệu</span>
                    </a>
                </div>

                <div class="mobile-nav-item">
                    <a href="/blog.php" class="mobile-nav-link <?= ($current_page == 'blog') ? 'active' : '' ?>">
                        <i class="fas fa-newspaper"></i>
                        <span>Tin tức</span>
                    </a>
                </div>

                <div class="mobile-nav-item">
                    <a href="/contact.php" class="mobile-nav-link <?= ($current_page == 'contact') ? 'active' : '' ?>">
                        <i class="fas fa-phone"></i>
                        <span>Liên hệ</span>
                    </a>
                </div>
            </div>

            <!-- User Account Section (if logged in) -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="mobile-account-section">
                    <div class="mobile-section-title">Tài khoản</div>

                    <div class="mobile-nav-item">
                        <a href="/profile.php" class="mobile-nav-link">
                            <i class="fas fa-user"></i>
                            <span>Hồ sơ cá nhân</span>
                        </a>
                    </div>

                    <div class="mobile-nav-item">
                        <a href="/appointments.php" class="mobile-nav-link">
                            <i class="fas fa-calendar-check"></i>
                            <span>Lịch hẹn</span>
                        </a>
                    </div>

                    <div class="mobile-nav-item">
                        <a href="/medical-records.php" class="mobile-nav-link">
                            <i class="fas fa-file-medical"></i>
                            <span>Hồ sơ bệnh án</span>
                        </a>
                    </div>

                    <div class="mobile-nav-item">
                        <a href="/orders.php" class="mobile-nav-link">
                            <i class="fas fa-shopping-bag"></i>
                            <span>Đơn hàng</span>
                        </a>
                    </div>

                    <?php
                    $is_admin = false;
                    if (isset($_SESSION['role_name'])) {
                        if ($_SESSION['role_name'] === 'admin' || $_SESSION['role_name'] === 'Admin') {
                            $is_admin = true;
                        }
                    } elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                        $is_admin = true;
                    } elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                        $is_admin = true;
                    }

                    if ($is_admin): ?>
                        <div class="mobile-nav-item admin-item">
                            <a href="/admin/dashboard.php" class="mobile-nav-link">
                                <i class="fas fa-cogs"></i>
                                <span>Quản trị</span>
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="mobile-nav-item logout-item">
                        <a href="#" onclick="event.preventDefault(); showLogoutModal();" class="mobile-nav-link">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Đăng xuất</span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Footer Section -->
            <div class="mobile-menu-footer">
                <div class="mobile-footer-links">
                    <a href="tel:1800-6928" class="mobile-footer-link">
                        <i class="fas fa-phone"></i>
                        <span>Hotline: 1800-6928</span>
                    </a>
                    <a href="mailto:support@vetcarestore.com" class="mobile-footer-link">
                        <i class="fas fa-envelope"></i>
                        <span>support@vetcarestore.com</span>
                    </a>
                </div>
                <div class="mobile-footer-text">
                    © 2024 VetCare Store. All rights reserved.
                </div>
            </div>
        </div>

        <!-- Mobile Menu Backdrop -->
        <div class="mobile-menu-backdrop" id="mobileMenuBackdrop"></div>
        </div>
        </div>


        <!-- Navigation Bar (Desktop Only) -->
        <div class="nav-bar" id="navBar">
            <div class="container-fluid nav-content">
                <nav class="main-nav">
                    <div class="nav-item">
                        <a href="/index.php" class="nav-link <?= ($current_page == 'index') ? 'active' : '' ?>">
                            <i class="fas fa-home"></i>Trang chủ
                        </a>
                    </div>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle <?= ($current_page == 'services') ? 'active' : '' ?>" data-bs-toggle="dropdown">
                            <i class="fas fa-paw"></i>Dịch vụ thú cưng
                        </a>

                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="/services.php">
                                    <i class="fas fa-list"></i>Tất cả dịch vụ
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <a class="dropdown-item" href="/services.php">
                                    <i class="fas fa-stethoscope"></i>Khám & điều trị
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="/services.php">
                                    <i class="fas fa-syringe"></i>Tiêm phòng
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="/services.php">
                                    <i class="fas fa-cut"></i>Spa & grooming
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle <?= ($current_page == 'shop') ? 'active' : '' ?>" data-bs-toggle="dropdown">
                            <i class="fas fa-paw"></i>Cửa hàng thú cưng
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="/shop.php">
                                    <i class="fas fa-box-open"></i>Tất cả sản phẩm
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="/shop/products.php?category=1">
                                    <i class="fas fa-stethoscope"></i>Thuốc thú y
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="/shop/products.php?category=2">
                                    <i class="fas fa-drumstick-bite"></i>Thực phẩm bổ sung thú cưng
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="/shop/products.php?category=3">
                                    <i class="fas fa-bone"></i>Thiết bị thú y
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/shop/products.php?category=4">
                                    <i class="fas fa-bone"></i>Vật tư thú y
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <a class="dropdown-item" href="/orders.php">
                                    <i class="fas fa-shopping-bag"></i>Đơn hàng của tôi
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="nav-item">
                        <a href="/about.php" class="nav-link <?= ($current_page == 'about') ? 'active' : '' ?>">
                            <i class="fas fa-info-circle"></i>Giới thiệu
                        </a>
                    </div>

                    <div class="nav-item">
                        <a href="/blog.php" class="nav-link <?= ($current_page == 'blog') ? 'active' : '' ?>">
                            <i class="fas fa-newspaper"></i>Tin tức
                        </a>
                    </div>

                    <div class="nav-item">
                        <a href="/contact.php" class="nav-link <?= ($current_page == 'contact') ? 'active' : '' ?>">
                            <i class="fas fa-phone"></i>Liên hệ
                        </a>
                    </div>
                </nav>
            </div>
        </div>

    </header>

    <?php if (isset($_SESSION['user_id'])): ?>

        <?php include __DIR__ . '/logout_modal.php'; ?>

        <script src="/assets/js/logout.js"></script>

    <?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Mobile search functions
        function toggleMobileSearch() {
            const overlay = document.getElementById('mobileSearchOverlay');
            const input = document.getElementById('mobileSearchInput');
            if (overlay && input) {
                overlay.style.display = 'flex';
                setTimeout(() => {
                    input.focus();
                    input.select();
                }, 50);
            }
        }

        function closeMobileSearch() {
            const overlay = document.getElementById('mobileSearchOverlay');
            if (overlay) {
                overlay.style.display = 'none';
            }
        }

        // Close search when clicking outside
        document.addEventListener('click', function(e) {
            const overlay = document.getElementById('mobileSearchOverlay');
            const searchBtn = document.querySelector('.search-toggle-btn');
            if (overlay && overlay.style.display === 'flex' &&
                !overlay.contains(e.target) &&
                !searchBtn.contains(e.target)) {
                closeMobileSearch();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Add header class to body for styling reference
            document.body.classList.add('has-fixed-header');

            // Mobile menu and search state
            const mobileToggle = document.getElementById('mobileToggle');
            const navBar = document.getElementById('navBar');
            const searchToggleBtn = document.querySelector('.search-toggle-btn');
            const searchBar = document.querySelector('.search-bar');
            let isMenuOpen = false;
            let isSearchOpen = false;

            // Mobile menu toggle
            if (mobileToggle) {
                mobileToggle.addEventListener('click', function() {
                    const mobileSideMenu = document.getElementById('mobileSideMenu');
                    const mobileMenuBackdrop = document.getElementById('mobileMenuBackdrop');

                    if (mobileSideMenu && mobileMenuBackdrop) {
                        // Toggle mobile side menu
                        mobileSideMenu.classList.toggle('active');
                        mobileMenuBackdrop.classList.toggle('active');
                        this.classList.toggle('active');

                        // Prevent body scroll when menu is open
                        if (mobileSideMenu.classList.contains('active')) {
                            document.body.style.overflow = 'hidden';
                        } else {
                            document.body.style.overflow = '';
                        }
                    }

                    // Close search if open
                    if (isSearchOpen && searchBar && searchToggleBtn) {
                        isSearchOpen = false;
                        searchBar.classList.remove('mobile-active');
                        searchToggleBtn.classList.remove('active');
                    }
                });
            }

            // Mobile menu close button
            const mobileMenuClose = document.getElementById('mobileMenuClose');
            if (mobileMenuClose) {
                mobileMenuClose.addEventListener('click', function() {
                    const mobileSideMenu = document.getElementById('mobileSideMenu');
                    const mobileMenuBackdrop = document.getElementById('mobileMenuBackdrop');
                    const mobileToggle = document.getElementById('mobileToggle');

                    if (mobileSideMenu && mobileMenuBackdrop) {
                        mobileSideMenu.classList.remove('active');
                        mobileMenuBackdrop.classList.remove('active');
                        document.body.style.overflow = '';
                    }

                    if (mobileToggle) {
                        mobileToggle.classList.remove('active');
                    }
                });
            }

            // Mobile menu backdrop click
            const mobileMenuBackdrop = document.getElementById('mobileMenuBackdrop');
            if (mobileMenuBackdrop) {
                mobileMenuBackdrop.addEventListener('click', function() {
                    const mobileSideMenu = document.getElementById('mobileSideMenu');
                    const mobileToggle = document.getElementById('mobileToggle');

                    if (mobileSideMenu) {
                        mobileSideMenu.classList.remove('active');
                        this.classList.remove('active');
                        document.body.style.overflow = '';
                    }

                    if (mobileToggle) {
                        mobileToggle.classList.remove('active');
                    }
                });
            }

            // Mobile dropdown toggles
            document.querySelectorAll('.mobile-dropdown-toggle').forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();

                    const submenu = this.parentElement.querySelector('.mobile-submenu');
                    const isActive = this.classList.contains('active');

                    // Close all other submenus
                    document.querySelectorAll('.mobile-dropdown-toggle').forEach(function(otherToggle) {
                        if (otherToggle !== toggle) {
                            otherToggle.classList.remove('active');
                            const otherSubmenu = otherToggle.parentElement.querySelector('.mobile-submenu');
                            if (otherSubmenu) {
                                otherSubmenu.classList.remove('active');
                            }
                        }
                    });

                    // Toggle current submenu
                    if (submenu) {
                        if (isActive) {
                            this.classList.remove('active');
                            submenu.classList.remove('active');
                        } else {
                            this.classList.add('active');
                            submenu.classList.add('active');
                        }
                    }
                });
            });

            // Mobile search toggle
            if (searchToggleBtn) {
                searchToggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Close menu if open
                    if (isMenuOpen && navBar && mobileToggle) {
                        isMenuOpen = false;
                        navBar.classList.remove('show');
                        mobileToggle.classList.remove('active');
                    }

                    // Open mobile search overlay
                    toggleMobileSearch();
                });
            }

            // Search functionality
            const searchForm = document.querySelector('.search-bar form');
            const searchInput = document.querySelector('.search-input');

            if (searchForm) {
                searchForm.addEventListener('submit', function(e) {
                    const query = searchInput.value.trim();
                    if (!query) {
                        e.preventDefault();
                        searchInput.focus();
                    }
                });
            }

            // Suggestion tags
            document.querySelectorAll('.suggestion-tag').forEach(tag => {
                tag.addEventListener('click', function() {
                    const query = this.textContent.trim();
                    searchInput.value = query;
                    searchForm.submit();
                });
            });

            // Initialize dropdown functionality
            function initializeDropdowns() {
                // Clear all existing event listeners first
                document.querySelectorAll('.dropdown-toggle').forEach(function(dropdownToggle) {
                    dropdownToggle.removeEventListener('click', handleDesktopDropdownClick);
                    dropdownToggle.removeEventListener('click', handleMobileDropdownClick);
                });

                if (window.innerWidth >= 1000) {
                    // Desktop dropdown functionality
                    document.querySelectorAll('.dropdown-toggle').forEach(function(dropdownToggle) {
                        dropdownToggle.addEventListener('click', handleDesktopDropdownClick);
                    });

                    document.addEventListener('click', handleDropdownOutsideClick);
                    console.log('Desktop dropdowns initialized');
                } else {
                    // Mobile dropdown functionality (including <720px)
                    document.querySelectorAll('.dropdown-toggle').forEach(function(dropdownToggle) {
                        dropdownToggle.addEventListener('click', handleMobileDropdownClick);
                    });

                    // Remove desktop outside click handler for mobile
                    document.removeEventListener('click', handleDropdownOutsideClick);
                    console.log('Mobile dropdowns initialized for screen width:', window.innerWidth);
                }
            }

            // Desktop dropdown handler
            function handleDesktopDropdownClick(e) {
                e.preventDefault();
                e.stopPropagation();

                const dropdownMenu = this.nextElementSibling;

                // Close all other dropdowns
                document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
                    if (menu !== dropdownMenu) {
                        menu.classList.remove('show');
                    }
                });

                // Toggle current dropdown
                dropdownMenu.classList.toggle('show');
            }

            // Mobile dropdown handler (works for all screen sizes < 992px)
            function handleMobileDropdownClick(e) {
                e.preventDefault();
                e.stopPropagation();

                console.log('Mobile dropdown clicked on screen width:', window.innerWidth);

                const dropdownMenu = this.nextElementSibling;
                const isCurrentlyOpen = dropdownMenu.classList.contains('show');

                // Close all other mobile dropdowns
                document.querySelectorAll('.nav-bar .dropdown-menu').forEach(function(menu) {
                    if (menu !== dropdownMenu) {
                        menu.classList.remove('show');
                        if (menu.previousElementSibling) {
                            menu.previousElementSibling.classList.remove('active');
                        }
                    }
                });

                // Toggle current dropdown
                if (isCurrentlyOpen) {
                    dropdownMenu.classList.remove('show');
                    this.classList.remove('active');
                    console.log('Closed dropdown');
                } else {
                    dropdownMenu.classList.add('show');
                    this.classList.add('active');
                    console.log('Opened dropdown');

                    // Force display for very small screens
                    if (window.innerWidth <= 750) {
                        dropdownMenu.style.display = 'block';
                        dropdownMenu.style.visibility = 'visible';
                        dropdownMenu.style.opacity = '1';
                        dropdownMenu.style.pointerEvents = 'auto';
                    }
                }
            }

            // Handle clicks outside dropdowns
            function handleDropdownOutsideClick(e) {
                if (!e.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
                        menu.classList.remove('show');
                    });
                    document.querySelectorAll('.dropdown-toggle').forEach(function(toggle) {
                        toggle.classList.remove('active');
                    });
                }
            }

            // Initialize dropdowns on page load
            initializeDropdowns();

            // Force close all dropdowns on initial load
            setTimeout(function() {
                document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
                    menu.classList.remove('show');
                });
                document.querySelectorAll('.dropdown-toggle').forEach(function(toggle) {
                    toggle.classList.remove('active');
                });
                console.log('All dropdowns force closed on page load');
            }, 100);

            // Close menus when clicking outside (mobile only)
            document.addEventListener('click', function(e) {
                let shouldClose = false;

                if (!e.target.closest('.mobile-toggle') &&
                    !e.target.closest('.nav-bar') &&
                    !e.target.closest('.search-toggle-btn') &&
                    !e.target.closest('.search-bar') &&
                    !e.target.closest('.mobile-search-container') &&
                    !e.target.closest('.custom-dropdown')) {
                    shouldClose = true;
                }

                if (shouldClose && window.innerWidth < 1000) {
                    if (isMenuOpen && navBar && mobileToggle) {
                        isMenuOpen = false;
                        navBar.classList.remove('show');
                        mobileToggle.classList.remove('active');
                    }

                    // Close mobile search if open
                    const mobileSearchOverlay = document.getElementById('mobileSearchOverlay');
                    if (mobileSearchOverlay && mobileSearchOverlay.classList.contains('active')) {
                        closeMobileSearch();
                    }
                }
            });

            // Handle window resize
            let resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function() {
                    if (window.innerWidth > 999) {
                        // Reset mobile states on desktop
                        if (mobileToggle) mobileToggle.classList.remove('active');
                        if (navBar) navBar.classList.remove('show');
                        if (searchToggleBtn) searchToggleBtn.classList.remove('active');

                        // Close mobile search overlay
                        const mobileSearchOverlay = document.getElementById('mobileSearchOverlay');
                        if (mobileSearchOverlay) {
                            mobileSearchOverlay.classList.remove('active');
                        }

                        // Close any open dropdowns
                        document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
                            menu.classList.remove('show');
                        });
                        document.querySelectorAll('.dropdown-toggle').forEach(function(toggle) {
                            toggle.classList.remove('active');
                        });

                        // Reinitialize dropdown functionality for desktop
                        initializeDropdowns();

                        isMenuOpen = false;
                        isSearchOpen = false;
                        console.log('Switched to desktop mode');
                    } else {
                        // Mobile mode - reinitialize mobile dropdowns
                        initializeDropdowns();
                        console.log('Switched to mobile mode - mobile dropdowns enabled');
                    }
                }, 100);
            });
        });

        // Mobile search functions
        function toggleMobileSearch() {
            const overlay = document.getElementById('mobileSearchOverlay');
            const input = document.getElementById('mobileSearchInput');
            const searchToggleBtn = document.querySelector('.search-toggle-btn');

            if (overlay) {
                overlay.classList.add('active');
                searchToggleBtn?.classList.add('active');

                // Focus input after animation
                setTimeout(() => {
                    if (input) {
                        input.focus();
                    }
                }, 300);
            }
        }

        function closeMobileSearch() {
            const overlay = document.getElementById('mobileSearchOverlay');
            const searchToggleBtn = document.querySelector('.search-toggle-btn');

            if (overlay) {
                overlay.classList.remove('active');
                searchToggleBtn?.classList.remove('active');
            }
        }

        // Close mobile search when clicking overlay
        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.getElementById('mobileSearchOverlay');
            if (overlay) {
                overlay.addEventListener('click', function(e) {
                    if (e.target === overlay) {
                        closeMobileSearch();
                    }
                });
            }
        });

        function toggleUserMenu(event) {
            event.stopPropagation();
            event.preventDefault();

            console.log('User menu clicked on screen width:', window.innerWidth);

            const menu = document.getElementById('userMenu');
            if (!menu) return;

            const isActive = menu.classList.contains('active');

            // Close all other dropdowns first (including Bootstrap dropdowns)
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => {
                m.classList.remove('active');
            });
            document.querySelectorAll('.dropdown-menu').forEach(m => {
                m.classList.remove('show');
            });

            // Toggle current menu
            if (!isActive) {
                menu.classList.add('active');
                console.log('User menu opened');

                // Force display for very small screens
                if (window.innerWidth <= 750) {
                    menu.style.display = 'block';
                    menu.style.visibility = 'visible';
                    menu.style.opacity = '1';
                    menu.style.pointerEvents = 'auto';
                    menu.style.transform = 'translateY(0)';
                }

                // Add backdrop for mobile
                if (window.innerWidth <= 768) {
                    const backdrop = document.createElement('div');
                    backdrop.className = 'user-menu-backdrop';
                    backdrop.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.5);
                    z-index: 999998;
                `;
                    document.body.appendChild(backdrop);

                    backdrop.addEventListener('click', function() {
                        menu.classList.remove('active');
                        backdrop.remove();
                        console.log('User menu closed via backdrop');
                    });
                }
            } else {
                menu.classList.remove('active');
                console.log('User menu closed');

                // Remove backdrop if exists
                const backdrop = document.querySelector('.user-menu-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
            }

            // Close when clicking outside
            function closeMenu(e) {
                if (!e.target.closest('.custom-dropdown')) {
                    menu.classList.remove('active');
                    document.removeEventListener('click', closeMenu);
                    console.log('User menu closed via outside click');

                    // Remove backdrop if exists
                    const backdrop = document.querySelector('.user-menu-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                }
            }

            // Only add outside click listener for desktop
            if (window.innerWidth > 768) {
                document.addEventListener('click', closeMenu);
            }

            // Close with escape key
            function closeWithEscape(e) {
                if (e.key === 'Escape') {
                    menu.classList.remove('active');
                    document.removeEventListener('keydown', closeWithEscape);
                    document.removeEventListener('click', closeMenu);
                    console.log('User menu closed via escape key');

                    const backdrop = document.querySelector('.user-menu-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                }
            }

            document.addEventListener('keydown', closeWithEscape);
        }

        let lastScrollTop = 0;
        const header = document.querySelector(".medical-header");
        let ticking = false;

        window.addEventListener("scroll", function() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

                    if (currentScroll > lastScrollTop && currentScroll > 100) {
                        // Cuộn xuống -> Ẩn header
                        header.style.top = "-160px";
                    } else if (lastScrollTop - currentScroll > 10) {
                        // Cuộn lên nhiều hơn 500px -> Hiện lại
                        header.style.top = "0";
                    }

                    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
                    ticking = false;
                });
                ticking = true;
            }
        });
    </script>