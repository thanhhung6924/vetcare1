    <?php
    // 1. Tự động lấy tên file hiện tại (ví dụ: posts.php)
    $current_page = basename($_SERVER['PHP_SELF']);

    // 2. Tự động lấy tên thư mục chứa file đó (ví dụ: blog hoặc admin)
    $current_dir = basename(dirname($_SERVER['PHP_SELF']));

    // Logic kiểm tra xem có đang ở trong các mục con của Blog hay không
    $is_blog_path = ($current_dir == 'blog');
    ?>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-content">
            <div class="sidebar-user">
                <div class="user-avatar">
                    <div class="avatar-lg">
                        <div class="avatar-title bg-primary text-white rounded-circle">
                            <?= strtoupper(substr($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'A', 0, 1)) ?>
                        </div>
                    </div>
                </div>
                <div class="user-info">
                    <h6 class="mb-0"><?= htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'Quản trị viên') ?></h6>
                    <span class="text-muted small">Administrator</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav flex-column">

                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>" href="/admin/dashboard.php">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= in_array($current_page, ['users.php', 'user-add.php', 'user-edit.php', 'user-view.php']) ? 'active' : '' ?>"
                            href="#userSubmenu" data-bs-toggle="collapse" aria-expanded="<?= in_array($current_page, ['users.php', 'user-add.php', 'user-edit.php', 'user-view.php']) ? 'true' : 'false' ?>">
                            <i class="fas fa-users"></i>
                            <span>Quản lý người dùng</span>
                            <i class="fas fa-chevron-down ms-auto"></i>
                        </a>
                        <ul class="collapse nav flex-column ms-3 <?= in_array($current_page, ['users.php', 'user-add.php', 'user-edit.php', 'user-view.php']) ? 'show' : '' ?>" id="userSubmenu">
                            <li class="nav-item"><a class="nav-link" href="/admin/users.php">Tất cả người dùng</a></li>
                            <li class="nav-item"><a class="nav-link" href="/admin/users.php?role=patient">Khách hàng</a></li>
                            <li class="nav-item"><a class="nav-link" href="/admin/users.php?role=doctor">Bác sĩ</a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'services.php' || $current_page == 'service-categories.php') ? 'active' : '' ?>"
                            href="#serviceSubmenu" data-bs-toggle="collapse">
                            <i class="fas fa-medical-kit"></i>
                            <span>Quản lý dịch vụ</span>
                            <i class="fas fa-chevron-down ms-auto"></i>
                        </a>
                        <ul class="collapse nav flex-column ms-3 <?= ($current_page == 'services.php' || $current_page == 'service-categories.php') ? 'show' : '' ?>" id="serviceSubmenu">
                            <li class="nav-item"><a class="nav-link" href="/admin/services.php">Danh sách dịch vụ</a></li>
                            <li class="nav-item"><a class="nav-link" href="/admin/service-categories.php">Danh mục dịch vụ</a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'products.php' || $current_page == 'product-categories.php') ? 'active' : '' ?>"
                            href="#productSubmenu" data-bs-toggle="collapse">
                            <i class="fas fa-pills"></i>
                            <span>Quản lý sản phẩm</span>
                            <i class="fas fa-chevron-down ms-auto"></i>
                        </a>
                        <ul class="collapse nav flex-column ms-3 <?= ($current_page == 'products.php' || $current_page == 'product-categories.php') ? 'show' : '' ?>" id="productSubmenu">
                            <li class="nav-item"><a class="nav-link" href="/admin/products.php">Danh sách sản phẩm</a></li>
                            <li class="nav-item"><a class="nav-link" href="/admin/product-categories.php">Danh mục sản phẩm</a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= $is_blog_path ? 'active' : '' ?>"
                            href="#contentSubmenu" data-bs-toggle="collapse">
                            <i class="fas fa-edit"></i>
                            <span>Quản lý nội dung</span>
                            <i class="fas fa-chevron-down ms-auto"></i>
                        </a>
                        <ul class="collapse nav flex-column ms-3 <?= $is_blog_path ? 'show' : '' ?>" id="contentSubmenu">
                            <li class="nav-item">
                                <a class="nav-link <?= $current_page == 'posts.php' ? 'active' : '' ?>" href="/admin/blog/posts.php">Bài viết blog</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $current_page == 'categories.php' && $is_blog_path ? 'active' : '' ?>" href="/admin/blog/categories.php">Danh mục blog</a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'orders.php' ? 'active' : '' ?>" href="/admin/orders.php">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Quản lý đơn hàng</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'appointments.php' ? 'active' : '' ?>" href="/admin/appointments.php">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Quản lý lịch hẹn</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'email-logs.php' ? 'active' : '' ?>" href="/admin/email-logs.php">
                            <i class="fas fa-envelope"></i>
                            <span>Lịch sử Email</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'rules.php' ? 'active' : '' ?>" href="/admin/rules.php">
                            <i class="fas fa-gavel"></i>
                            <span>Bộ luật</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <div class="d-grid gap-2">
                    <a href="/index.php" class="btn btn-outline-light btn-sm" target="_blank">
                        <i class="fas fa-external-link-alt me-1"></i> Xem website
                    </a>
                    <a href="/logout.php" class="btn btn-danger btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i> Đăng xuất
                    </a>
                </div>
                <div class="text-center mt-3">
                    <small style="color: #666;">© 2026 VetCare Store</small>
                </div>
            </div>
        </div>
    </aside>