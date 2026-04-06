<?php
// Start session trước khi có bất kỳ output nào
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/db.php';
require_once '../includes/functions/product_functions.php';
require_once '../includes/functions/format_helpers.php';

// Xử lý các tham số
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : PHP_FLOAT_MAX;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';

// Lấy khoảng giá cho slider
$price_range = getProductPriceRange();
if (!isset($_GET['max_price'])) {
    $max_price = $price_range['max'];
}

// Lấy danh sách sản phẩm với filter
$result = getFilteredProducts($min_price, $max_price, $sort, $category_id, $page);
$products = $result['products'];
$total_pages = $result['total_pages'];

// Lấy danh mục và sản phẩm phổ biến
$categories = getCategories();
$popular_products = getPopularProducts(3);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm - VetCare Store</title>

    <link rel="stylesheet" href="../assets/css/layout.css">
    <link rel="stylesheet" href="../assets/css/shop.css">
    <link rel="stylesheet" href="../assets/css/products.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider@14.6.3/distribute/nouislider.min.css">

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        /* Cart notification styles */
        .cart-notification {
            position: fixed !important;
            z-index: 99999999 !important;
            top: 20px !important;
            right: 20px !important;
            min-width: 300px !important;
            max-width: 400px !important;
            background: white !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12) !important;
            border-radius: 12px !important;
            backdrop-filter: blur(10px) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            font-weight: 500 !important;
            margin: 0 !important;
            padding: 1rem !important;
            display: flex !important;
            align-items: center !important;
            gap: 1rem !important;
            transform: translateY(-100%) !important;
            opacity: 0 !important;
            transition: all 0.3s ease !important;
        }

        .cart-notification.show {
            transform: translateY(0) !important;
            opacity: 1 !important;
        }

        .cart-notification .icon {
            width: 40px !important;
            height: 40px !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1.2rem !important;
            flex-shrink: 0 !important;
        }

        .cart-notification.success .icon {
            background: #d4edda !important;
            color: #155724 !important;
        }

        .cart-notification.error .icon {
            background: #f8d7da !important;
            color: #721c24 !important;
        }

        .cart-notification .content {
            flex: 1 !important;
        }

        .cart-notification .title {
            font-weight: 600 !important;
            margin-bottom: 0.2rem !important;
            color: #2d3748 !important;
        }

        .cart-notification .message {
            color: #718096 !important;
            font-size: 0.9rem !important;
        }

        .cart-notification .close {
            width: 24px !important;
            height: 24px !important;
            border-radius: 50% !important;
            background: #edf2f7 !important;
            color: #718096 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            font-size: 0.8rem !important;
            transition: all 0.2s ease !important;
        }

        .cart-notification .close:hover {
            background: #e2e8f0 !important;
            color: #2d3748 !important;
        }

        /* Loading button styles */
        .add-to-cart.loading {
            position: relative;
            color: transparent !important;
        }

        .add-to-cart.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 16px;
            height: 16px;
            margin-top: -8px;
            margin-left: -8px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s infinite linear;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Cart count animation */
        @keyframes cartBounce {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }
        }

        .cart-count.updating {
            animation: cartBounce 0.5s ease;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .cart-notification {
                left: 20px !important;
                right: 20px !important;
                min-width: auto !important;
            }

            /* Filter styles for mobile */
            .sidebar-filters {
                position: fixed !important;
                top: 100px !important;
                left: 0 !important;
                width: 100% !important;
                height: calc(100vh - 60px) !important;
                max-height: calc(100vh - 60px) !important;
                background: white !important;
                z-index: 1050 !important;
                transform: translateX(-100%) !important;
                transition: transform 0.3s ease !important;
                display: flex !important;
                flex-direction: column !important;
                padding: 0 !important;
                overflow: hidden !important;
                border-top: 1px solid #eee !important;
            }

            .sidebar-filters.show {
                transform: translateX(0) !important;
            }

            .filter-header {
                position: sticky !important;
                top: 0 !important;
                background: white !important;
                z-index: 10 !important;
                padding: 0.75rem 1rem !important;
                border-bottom: 1px solid #eee !important;
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
            }

            .filter-header .widget-title {
                font-size: 1.1rem !important;
                margin: 0 !important;
                font-weight: 600 !important;
            }

            .filter-content {
                flex: 1 !important;
                overflow-y: auto !important;
                padding: 1rem !important;
                -webkit-overflow-scrolling: touch !important;
                height: calc(100vh - 180px) !important;
                position: relative !important;
            }

            .filter-content-wrapper {
                padding-bottom: 1rem !important;
            }

            .filter-actions {
                position: sticky !important;
                bottom: 0 !important;
                background: white !important;
                padding: 1rem !important;
                border-top: 1px solid #eee !important;
                display: flex !important;
                gap: 1rem !important;
                box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1) !important;
                bottom: 30px !important;
            }

            .filter-actions button {
                flex: 1 !important;
            }

            .filter-overlay {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 100% !important;
                height: 100% !important;
                background: rgba(0, 0, 0, 0.5) !important;
                z-index: 1040 !important;
                opacity: 0 !important;
                visibility: hidden !important;
                transition: all 0.3s ease !important;
            }

            .filter-overlay.show {
                opacity: 1 !important;
                visibility: visible !important;
            }

            .filter-button {
                width: 100% !important;
                padding: 0.75rem 1rem !important;
                background: white !important;
                border: 1px solid #ddd !important;
                border-radius: 8px !important;
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                margin-bottom: 1rem !important;
                font-weight: 500 !important;
                color: #333 !important;
            }

            .category-list {
                margin: 0 !important;
                padding: 0 !important;
                list-style: none !important;
            }

            .category-list li {
                margin-bottom: 0.5rem !important;
            }

            .category-list a {
                padding: 0.5rem 0 !important;
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                color: #333 !important;
                text-decoration: none !important;
            }

            .category-list li.active a {
                color: #0d6efd !important;
                font-weight: 500 !important;
            }

            .price-inputs {
                display: flex !important;
                align-items: center !important;
                gap: 0.5rem !important;
                margin-top: 1rem !important;
                margin: 0.5rem 0 !important;
            }

            .price-inputs .input-group {
                flex: 1 !important;
                height: 40px !important;
            }

            .input-group input {
                height: 100% !important;
                border-radius: 4px !important;
                font-size: 0.9rem !important;
            }

            .filter-section {
                margin-bottom: 1rem !important;
                padding-bottom: 1rem !important;
                border-bottom: 1px solid #eee !important;
            }

            .filter-section:first-child {
                margin-top: 0 !important;
            }

            .filter-section:last-child {
                margin-bottom: 0 !important;
                padding-bottom: 0 !important;
                border-bottom: none !important;
            }

            .filter-title {
                font-size: 1rem !important;
                font-weight: 600 !important;
                margin-bottom: 1rem !important;
            }
        }

        /* Cart count badge */
        .cart-count {
            position: absolute !important;
            top: -8px !important;
            right: -8px !important;
            background: #e53e3e !important;
            color: white !important;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            min-width: 18px !important;
            height: 18px !important;
            border-radius: 9px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            z-index: 10 !important;
            border: 2px solid white !important;
            padding: 0 6px !important;
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <?php
    $appointment_modal_path = __DIR__ . '/../includes/appointment-modal.php';
    if (file_exists($appointment_modal_path)) {
        include $appointment_modal_path;
    }
    ?>

    <main class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3">
                    <button class="filter-button d-lg-none" id="filterButton">
                        <span><i class="fas fa-filter me-2"></i> Bộ lọc</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>

                    <div class="filter-overlay" id="filterOverlay"></div>

                    <div class="sidebar-filters" id="sidebarFilters">
                        <div class="filter-header">
                            <h4 class="widget-title">Bộ lọc</h4>
                            <button class="btn-close d-lg-none" id="closeFilter" aria-label="Close"></button>
                        </div>

                        <div class="filter-content">
                            <div class="filter-content-wrapper">
                                <div class="filter-section">
                                    <h5 class="filter-title">Danh mục sản phẩm</h5>
                                    <ul class="category-list">
                                        <li class="<?php echo !$category_id ? 'active' : ''; ?>">
                                            <a href="javascript:void(0)" class="category-filter" data-category="">
                                                Tất cả sản phẩm
                                                <span class="count">(<?php echo $result['total']; ?>)</span>
                                            </a>
                                        </li>
                                        <?php foreach ($categories as $category): ?>
                                            <li class="<?php echo $category_id == $category['category_id'] ? 'active' : ''; ?>">
                                                <a href="javascript:void(0)" class="category-filter" data-category="<?php echo $category['category_id']; ?>">
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                    <span class="count">(<?php echo getCategoryProductCount($category['category_id']); ?>)</span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>

                                <div class="filter-section">
                                    <h5 class="filter-title">Lọc theo giá</h5>
                                    <form method="GET" action="products.php" id="filterForm">
                                        <div class="price-inputs">
                                            <div class="input-group">
                                                <span class="input-group-text">₫</span>
                                                <input type="number" name="min_price" id="min-price" value="<?php echo $min_price; ?>" class="form-control" placeholder="Từ">
                                            </div>
                                            <span class="separator">-</span>
                                            <div class="input-group">
                                                <span class="input-group-text">₫</span>
                                                <input type="number" name="max_price" id="max-price" value="<?php echo $max_price; ?>" class="form-control" placeholder="Đến">
                                            </div>
                                        </div>
                                        <?php if ($category_id): ?><input type="hidden" name="category" value="<?php echo $category_id; ?>"><?php endif; ?>
                                        <?php if ($sort != 'default'): ?><input type="hidden" name="sort" value="<?php echo $sort; ?>"><?php endif; ?>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="filter-actions">
                            <button type="button" class="btn btn-outline-secondary" id="resetFilter">
                                <i class="fas fa-undo-alt"></i> Thiết lập lại
                            </button>
                            <button type="submit" form="filterForm" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Áp dụng
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="products-toolbar mb-4" data-aos="fade-up">
                        <div class="showing-results">
                            Hiển thị <?php echo count($products); ?> / <?php echo $result['total']; ?> sản phẩm
                        </div>
                        <div class="sorting">
                            <form method="GET" action="products.php" class="d-inline">
                                <select class="custom-select" name="sort" onchange="this.form.submit();">
                                    <option value="default" <?php echo $sort == 'default' ? 'selected' : ''; ?>>Mới nhất</option>
                                    <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Giá tăng dần</option>
                                    <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Giá giảm dần</option>
                                    <option value="name_asc" <?php echo $sort == 'name_asc' ? 'selected' : ''; ?>>Tên A-Z</option>
                                    <option value="name_desc" <?php echo $sort == 'name_desc' ? 'selected' : ''; ?>>Tên Z-A</option>
                                    <option value="rating" <?php echo $sort == 'rating' ? 'selected' : ''; ?>>Đánh giá cao</option>
                                </select>
                                <?php if ($category_id): ?><input type="hidden" name="category" value="<?php echo $category_id; ?>"><?php endif; ?>
                                <?php if (isset($_GET['min_price'])): ?><input type="hidden" name="min_price" value="<?php echo $_GET['min_price']; ?>"><?php endif; ?>
                                <?php if (isset($_GET['max_price'])): ?><input type="hidden" name="max_price" value="<?php echo $_GET['max_price']; ?>"><?php endif; ?>
                            </form>
                        </div>
                    </div>
                    <?php
                    $ai_products = [];
                    $displayed_ids = [];

                    // ✅ LẤY DATA TỪ CART (đã lưu trước đó)
                    $recommendations = $_SESSION['ai_recommendations'] ?? [];

                    if (!empty($recommendations)) {

                        foreach ($recommendations as $r_ai) {

                            // ❌ bỏ nếu thiếu name
                            if (empty($r_ai['name'])) continue;

                            $stmt_p = $conn->prepare("
            SELECT p.*, c.name as category_name, 
                   COALESCE(AVG(pr.rating), 0) as avg_rating,
                   COUNT(pr.review_id) as review_count
            FROM products p
            LEFT JOIN product_categories c 
                ON p.category_id = c.category_id
            LEFT JOIN product_reviews pr 
                ON p.product_id = pr.product_id
            WHERE p.name = ? AND p.stock > 0
            GROUP BY p.product_id 
            LIMIT 1
        ");

                            $stmt_p->bind_param("s", $r_ai['name']);
                            $stmt_p->execute();
                            $p_info = $stmt_p->get_result()->fetch_assoc();

                            if ($p_info) {

                                // ❌ tránh trùng
                                if (in_array($p_info['product_id'], $displayed_ids)) continue;

                                $p_info['display_image'] = !empty($p_info['image_url'])
                                    ? '/' . ltrim($p_info['image_url'], '/')
                                    : '/assets/images/default-product.jpg';
                                $p_info['is_ai_recommended'] = true;
                                $p_info['confidence'] = $r_ai['confidence'] ?? 0;
                                $p_info['type'] = $r_ai['type'] ?? 'GỢI Ý TỪ AI';

                                $ai_products[] = $p_info;
                                $displayed_ids[] = $p_info['product_id'];
                            }

                            if (count($ai_products) >= 3) break;
                        }
                    }

                    // ✅ merge như cũ
                    $filtered_normal_products = [];

                    foreach ($products as $p) {
                        if (!in_array($p['product_id'], $displayed_ids)) {
                            $filtered_normal_products[] = $p;
                        }
                    }

                    $final_product_list = array_merge($ai_products, $filtered_normal_products);
                    ?>
                    <div class="row g-4">
                        <?php foreach ($final_product_list as $index => $product): ?>
                            <div class="col-6 col-md-4" data-aos="fade-up" data-aos-delay="<?php echo min($index * 50, 300); ?>">

                                <div class="product-card <?php echo isset($product['is_ai_recommended']) ? 'border-primary border border-2' : ''; ?>"
                                    <?php echo isset($product['is_ai_recommended']) ? 'style="box-shadow: 0 5px 15px rgba(13, 110, 253, 0.15);"' : ''; ?>>

                                    <div class="product-image position-relative">
                                        <a href="details.php?id=<?php echo $product['product_id']; ?>">
                                            <img src="<?php echo htmlspecialchars($product['display_image'] ?? ($product['image_url'] ?? '../assets/images/default-product.jpg')); ?>"
                                                alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                class="img-fluid">
                                        </a>

                                        <?php
                                        $badgeOriginalPrice = floatval($product['price']);
                                        $badgeDiscountAmount = floatval($product['discount_amount'] ?? 0);
                                        $badgeDiscountPercent = $badgeOriginalPrice > 0 && $badgeDiscountAmount > 0 ? round(($badgeDiscountAmount / $badgeOriginalPrice) * 100) : 0;
                                        ?>

                                        <?php if ($badgeDiscountPercent > 0): ?>
                                            <div class="product-badge">
                                                -<?php echo $badgeDiscountPercent; ?>%
                                            </div>
                                        <?php endif; ?>

                                        <?php if (isset($product['is_ai_recommended'])): ?>
                                            <div class="product-badge bg-primary text-white" style="left: auto; right: 10px;">
                                                <i class="fas fa-magic me-1"></i> Gợi ý
                                            </div>
                                        <?php endif; ?>

                                        <div class="action-buttons">
                                            <button class="action-btn add-to-cart"
                                                data-id="<?php echo $product['product_id']; ?>"
                                                <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                            <button class="action-btn add-to-wishlist"
                                                data-id="<?php echo $product['product_id']; ?>">
                                                <i class="far fa-heart"></i>
                                            </button>
                                            <button class="action-btn quick-view"
                                                onclick="window.location.href='details.php?id=<?php echo $product['product_id']; ?>'">
                                                <i class="far fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="product-content">
                                        <a href="#" class="product-category">
                                            <i class="fas fa-tag me-1"></i>
                                            <?php echo htmlspecialchars($product['category_name'] ?? 'Sản phẩm'); ?>
                                        </a>

                                        <h3 class="product-title">
                                            <a href="details.php?id=<?php echo $product['product_id']; ?>">
                                                <?php echo htmlspecialchars($product['name']); ?>
                                            </a>
                                        </h3>

                                        <div class="product-rating">
                                            <div class="rating-stars">
                                                <?php
                                                $rating = floatval($product['avg_rating'] ?? 0);
                                                for ($i = 1; $i <= 5; $i++) {
                                                    if ($i <= floor($rating)) {
                                                        echo '<i class="fas fa-star"></i>';
                                                    } elseif ($i <= $rating) {
                                                        echo '<i class="fas fa-star-half-alt"></i>';
                                                    } else {
                                                        echo '<i class="far fa-star"></i>';
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <span class="rating-text">(<?php echo $product['review_count'] ?? 0; ?> đánh giá)</span>
                                        </div>

                                        <div class="product-price">
                                            <?php if ($badgeDiscountAmount > 0): ?>
                                                <span class="current-price"><?php echo format_currency(max(0, $badgeOriginalPrice - $badgeDiscountAmount)); ?></span>
                                                <span class="original-price"><?php echo format_currency($badgeOriginalPrice); ?></span>
                                            <?php else: ?>
                                                <span class="current-price"><?php echo format_currency($badgeOriginalPrice); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($total_pages > 1): ?>
                        <nav class="mt-4" data-aos="fade-up">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="products.php?page=<?php echo $page - 1; ?>&category=<?php echo $category_id; ?>&sort=<?php echo $sort; ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="products.php?page=<?php echo $i; ?>&category=<?php echo $category_id; ?>&sort=<?php echo $sort; ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="products.php?page=<?php echo $page + 1; ?>&category=<?php echo $category_id; ?>&sort=<?php echo $sort; ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <div class="modal fade" id="quickViewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/14.6.3/nouislider.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="../assets/js/cart-new.js"></script>
    <script>
        // Hàm hiển thị thông báo giỏ hàng
        function showCartNotification(type, message) {
            $('.cart-notification').remove();
            const notification = $(`
                <div class="cart-notification ${type}">
                    <div class="icon">
                        <i class="fas ${type === 'success' ? 'fa-check' : 'fa-exclamation-triangle'}"></i>
                    </div>
                    <div class="content">
                        <div class="title">${type === 'success' ? 'Thành công!' : 'Lỗi!'}</div>
                        <div class="message">${message}</div>
                    </div>
                    <div class="close">
                        <i class="fas fa-times"></i>
                    </div>
                </div>
            `);

            $('body').append(notification);
            setTimeout(() => notification.addClass('show'), 100);

            notification.find('.close').on('click', () => {
                notification.removeClass('show');
                setTimeout(() => notification.remove(), 300);
            });

            setTimeout(() => {
                notification.removeClass('show');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Khởi tạo AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });

        // Xử lý lọc danh mục bằng AJAX
        document.querySelectorAll('.category-filter').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const categoryId = this.dataset.category;
                const currentUrl = new URL(window.location.href);

                if (categoryId) {
                    currentUrl.searchParams.set('category', categoryId);
                } else {
                    currentUrl.searchParams.delete('category');
                }

                const minPrice = document.getElementById('min-price').value;
                const maxPrice = document.getElementById('max-price').value;
                const sort = document.querySelector('select[name="sort"]').value;

                if (minPrice) currentUrl.searchParams.set('min_price', minPrice);
                if (maxPrice) currentUrl.searchParams.set('max_price', maxPrice);
                if (sort !== 'default') currentUrl.searchParams.set('sort', sort);

                document.querySelectorAll('.category-filter').forEach(el => {
                    el.parentElement.classList.remove('active');
                });
                this.parentElement.classList.add('active');

                fetch(currentUrl)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        const productsGrid = document.querySelector('.products-toolbar').parentElement;
                        const newProductsGrid = doc.querySelector('.products-toolbar').parentElement;
                        productsGrid.innerHTML = newProductsGrid.innerHTML;

                        window.history.pushState({}, '', currentUrl);
                        initializeProductActions();
                        AOS.refresh();
                    })
                    .catch(error => console.error('Error:', error));
            });
        });

        function initializeProductActions() {
            document.querySelectorAll('.quick-view').forEach(btn => {
                btn.addEventListener('click', function() {
                    const productId = this.dataset.id;
                    const modal = new bootstrap.Modal(document.getElementById('quickViewModal'));

                    fetch(`../api/product.php?id=${productId}`)
                        .then(response => response.json())
                        .then(data => {
                            document.querySelector('#quickViewModal .modal-body').innerHTML = `
                                <div class="quick-view-content">
                                    </div>
                            `;
                            modal.show();
                        });
                });
            });

            $('.add-to-cart').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const $btn = $(this);
                const productId = $btn.data('id');
                const quantity = 1;

                $btn.addClass('loading').prop('disabled', true);

                $.ajax({
                    url: '/api/cart/add.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        product_id: productId,
                        quantity: quantity
                    }),
                    success: function(response) {
                        if (response.success) {
                            showCartNotification('success', 'Đã thêm vào giỏ hàng');
                            updateCartCount();
                        } else {
                            showCartNotification('error', response.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng');
                        }
                    },
                    error: function() {
                        showCartNotification('error', 'Không thể kết nối đến server');
                    },
                    complete: function() {
                        $btn.removeClass('loading').prop('disabled', false);
                    }
                });
            });
        }

        const priceSlider = document.getElementById('price-slider');
        if (priceSlider) {
            noUiSlider.create(priceSlider, {
                start: [<?php echo $min_price; ?>, <?php echo $max_price; ?>],
                connect: true,
                range: {
                    'min': <?php echo $price_range['min']; ?>,
                    'max': <?php echo $price_range['max']; ?>
                },
                format: {
                    to: value => Math.round(value),
                    from: value => Math.round(value)
                }
            });

            priceSlider.noUiSlider.on('update', function(values, handle) {
                document.getElementById(handle ? 'max-price' : 'min-price').value = values[handle];
            });
        }

        document.querySelectorAll('.quick-view').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.dataset.id;
                const modal = new bootstrap.Modal(document.getElementById('quickViewModal'));

                fetch(`../api/product.php?id=${productId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.querySelector('#quickViewModal .modal-body').innerHTML = `
                            <div class="quick-view-content">
                                </div>
                        `;
                        modal.show();
                    });
            });
        });

        const filterButton = document.getElementById('filterButton');
        const closeFilter = document.getElementById('closeFilter');
        const sidebarFilters = document.getElementById('sidebarFilters');
        const filterOverlay = document.getElementById('filterOverlay');

        function closeFilterMenu() {
            filterButton.classList.remove('active');
            sidebarFilters.classList.remove('show');
            filterOverlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        if (filterButton && sidebarFilters && filterOverlay) {
            filterButton.addEventListener('click', function() {
                this.classList.toggle('active');
                sidebarFilters.classList.toggle('show');
                filterOverlay.classList.toggle('show');
                document.body.style.overflow = sidebarFilters.classList.contains('show') ? 'hidden' : '';
            });

            filterOverlay.addEventListener('click', closeFilterMenu);
            closeFilter.addEventListener('click', closeFilterMenu);

            document.querySelectorAll('.category-filter').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        closeFilterMenu();
                    }
                });
            });
        }
    </script>
</body>

</html>