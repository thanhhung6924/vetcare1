<?php
session_start();
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dịch vụ y tế - VetCare Store</title>
    <meta name="description" content="Các dịch vụ y tế chất lượng cao tại VetCare Store - Khám chữa bệnh, tư vấn sức khỏe với bác sĩ chuyên môn">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/services.css">
</head>

<body>
    <?php include 'includes/header.php';

    // Fetch services data
    $sql = "SELECT 
            c.id as category_id,
            c.name as category_name,
            c.icon as category_icon,
            c.description as category_description,
            s.id as service_id,
            s.name as service_name,
            s.short_description,
            s.price_from,
            s.price_to,
            s.is_featured,
            s.is_emergency,
            GROUP_CONCAT(sf.feature_name) as features
            FROM service_categories c
            LEFT JOIN services s ON s.category_id = c.id
            LEFT JOIN service_features sf ON sf.service_id = s.id
            WHERE c.is_active = 1
            GROUP BY s.id
            ORDER BY c.display_order, s.display_order";

    $result = $conn->query($sql);
    $services = array();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categoryId = $row['category_id'];
            if (!isset($services[$categoryId])) {
                $services[$categoryId] = array(
                    'category' => array(
                        'name' => $row['category_name'],
                        'icon' => $row['category_icon'],
                        'description' => $row['category_description']
                    ),
                    'items' => array()
                );
            }
            if ($row['service_id']) {
                $priceRange = 'Liên hệ';
                if ($row['price_from'] !== null) {
                    $priceRange = number_format($row['price_from'], 0, ',', '.') . 'đ';
                    if ($row['price_to'] !== null) {
                        $priceRange .= ' - ' . number_format($row['price_to'], 0, ',', '.') . 'đ';
                    }
                }

                $features = $row['features'] ? explode(',', $row['features']) : [];

                $services[$categoryId]['items'][] = array(
                    'name' => $row['service_name'],
                    'description' => $row['short_description'],
                    'features' => $features,
                    'price_range' => $priceRange,
                    'is_featured' => $row['is_featured'],
                    'is_emergency' => $row['is_emergency']
                );
            }
        }
    }
    ?>

    <!-- Trang chủ dịch vụ -->
    <section class="hero-section">
        <div class="hero-background">
            <img src="./assets/images/services-bg copy.jpg" alt="Pet Care Background" class="hero-bg-image">
            <div class="hero-overlay"></div>
        </div>
        <div class="container">
            <div class="hero-content">
                <div class="hero-icon">
                    <i class="fas fa-paw"></i>
                </div>
                <h1 class="hero-title">Dịch Vụ Chăm Sóc Thú Cưng Chuyên Nghiệp</h1>
                <div class="hero-divider"></div>
                <p class="hero-subtitle">
                    Yêu thương và chăm sóc toàn diện cho người bạn nhỏ của bạn với đội ngũ bác sĩ thú y tận tâm
                </p>
                <div class="hero-features">
                    <div class="feature-item">
                        <i class="fas fa-user-md"></i>
                        <span>Bác sĩ thú y giỏi</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-clock"></i>
                        <span>Cấp cứu 24/7</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-shampoo"></i> <span>Spa & Pet Shop hiện đại</span>
                    </div>
                </div>
                <div class="hero-buttons">
                    <a href="#services" class="btn btn-primary">
                        <i class="fas fa-bone"></i> Khám phá dịch vụ
                    </a>
                    <a href="tel:0123456789" class="btn btn-outline">
                        <i class="fas fa-phone"></i> Tư vấn miễn phí
                    </a>
                </div>
            </div>
        </div>
    </section>
    <section id="services" class="services-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Dịch Vụ Tại VetCare Store</h2>
                <p class="section-description">
                    Chúng tôi cung cấp đầy đủ các giải pháp y tế và làm đẹp tốt nhất cho thú cưng
                </p>
            </div>

            <div class="row">
                <?php foreach ($services as $categoryId => $categoryData): ?>
                    <?php foreach ($categoryData['items'] as $service): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="service-card">
                                <div class="service-icon">
                                    <i class="<?php echo htmlspecialchars($categoryData['category']['icon']); ?>"></i>
                                </div>
                                <h3 class="service-title">
                                    <?php echo htmlspecialchars($service['name']); ?>
                                </h3>
                                <p class="service-description">
                                    <?php echo htmlspecialchars($service['description']); ?>
                                </p>
                                <?php if (!empty($service['features'])): ?>
                                    <ul class="service-features">
                                        <?php foreach ($service['features'] as $feature): ?>
                                            <li>
                                                <i class="fas fa-check"></i>
                                                <?php echo htmlspecialchars($feature); ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                                <div class="service-price">
                                    <i class="fas fa-tag"></i> Giá từ: <?php echo htmlspecialchars($service['price_range']); ?>
                                </div>
                                <a href="tel:0123456789" class="btn-book">
                                    <i class="fas fa-calendar-alt"></i> Đặt lịch ngay: 0123 456 789
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Danh sách dịch vụ -->


    <!-- Ưu điểm -->
    <section class="features-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Tại Sao Chọn VetCare Store</h2>
                <p class="section-description">
                    Chúng tôi cam kết mang lại môi trường chăm sóc tốt nhất cho "người bạn nhỏ" của bạn
                </p>
            </div>

            <div class="row">
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <h3 class="feature-title">Bác Sĩ Thú Y Giỏi</h3>
                        <p class="feature-description">
                            Đội ngũ bác sĩ chuyên môn cao, giàu kinh nghiệm và yêu động vật
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-ambulance"></i>
                        </div>
                        <h3 class="feature-title">Cấp Cứu 24/7</h3>
                        <p class="feature-description">
                            Luôn sẵn sàng hỗ trợ thú cưng của bạn trong mọi tình huống khẩn cấp
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-tags"></i>
                        </div>
                        <h3 class="feature-title">Giá Cả Minh Bạch</h3>
                        <p class="feature-description">
                            Chi phí dịch vụ và sản phẩm hợp lý, niêm yết rõ ràng
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-paw"></i>
                        </div>
                        <h3 class="feature-title">Chăm Sóc Tận Tâm</h3>
                        <p class="feature-description">
                            Chăm sóc chu đáo, yêu thương thú cưng như chính thành viên gia đình
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">Bạn Cần Tư Vấn Sức Khỏe Thú Cưng?</h2>
            <p class="cta-description">
                Đừng ngần ngại, hãy gọi ngay cho VetCare để được bác sĩ tư vấn miễn phí
            </p>
            <div>
                <a href="tel:0123456789" class="btn btn-primary">
                    <i class="fas fa-phone-alt"></i> Gọi ngay: 0123 456 789
                </a>
                <a href="booking.php" class="btn btn-outline" id="bookingBtn">
                    <i class="fas fa-calendar-check"></i> Đặt lịch Spa/Khám bệnh
                </a>
            </div>
        </div>
    </section>

    <?php
    $conn->close();
    include 'includes/footer.php';
    ?>

    <!-- Modal thông báo bảo trì -->
    <div class="maintenance-modal" id="maintenanceModal">
        <div class="maintenance-modal-content">
            <h2 class="maintenance-modal-title">Thông Báo</h2>
            <p class="maintenance-modal-description">
                Chức năng đặt lịch online đang được bảo trì.
                Vui lòng gọi điện để được hỗ trợ đặt lịch.
            </p>
            <div>
                <a href="tel:0123456789" class="maintenance-modal-close">
                    <i class="fas fa-phone"></i> Gọi: 0123 456 789
                </a>
                <button class="maintenance-modal-close" onclick="closeMaintenance()">
                    Đóng
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Hiển thị modal khi click nút đặt lịch
        document.getElementById('bookingBtn').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('maintenanceModal').classList.add('active');
        });

        // Đóng modal
        function closeMaintenance() {
            document.getElementById('maintenanceModal').classList.remove('active');
        }
    </script>
</body>

</html>