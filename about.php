<?php
session_start();
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Về chúng tôi - VetCare Store Pet Clinic & Pet Shop</title>
    <meta name="description" content="Tìm hiểu về VetCare - Phòng khám y khoa hiện đại với đội ngũ bác sĩ chuyên nghiệp, mang đến dịch vụ chăm sóc sức khỏe toàn diện.">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/about.css">
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-background">
                <div class="hero-overlay"></div>
                <div class="floating-shapes">
                    <div class="shape shape-1"></div>
                    <div class="shape shape-2"></div>
                    <div class="shape shape-3"></div>
                </div>
            </div>
            <div class="container">
                <div class="row align-items-center min-vh-100">
                    <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000">
                        <div class="hero-content">
                            <div class="hero-badge">
                                <i class="fas fa-award"></i>
                                <span>Hơn 10 năm chăm sóc thú cưng</span>
                            </div>
                            <h1 class="hero-title">
                                Chăm sóc thú cưng
                                <span class="text-gradient">toàn diện & tận tâm</span>
                            </h1>
                            <p class="hero-subtitle">
                                VetCare là địa chỉ tin cậy dành cho thú cưng của bạn, cung cấp dịch vụ khám chữa bệnh,
                                chăm sóc sức khỏe và các sản phẩm thú y chính hãng. Với đội ngũ bác sĩ thú y giàu kinh nghiệm,
                                chúng tôi cam kết mang đến sự an tâm cho bạn và người bạn nhỏ của mình.
                            </p>
                            <div class="hero-buttons">
                                <a href="#mission" class="btn btn-primary btn-lg">
                                    <i class="fas fa-arrow-down me-2"></i>
                                    Tìm hiểu thêm
                                </a>
                                <a href="contact.php" class="btn btn-outline-light btn-lg">
                                    <i class="fas fa-phone me-2"></i>
                                    Liên hệ tư vấn
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                        <div class="hero-image-container">
                            <div class="hero-image">
                                <img src="assets/images/ok-Photoroom.png" alt="Đội ngũ bác sĩ thú y VetCare" class="img-fluid">
                                <div class="image-overlay">
                                    <div class="play-button" data-bs-toggle="modal" data-bs-target="#videoModal">
                                        <i class="fas fa-play"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="hero-stats-floating">
                                <div class="stat-card" data-aos="zoom-in" data-aos-delay="400">
                                    <div class="stat-icon">
                                        <i class="fas fa-user-md"></i>
                                    </div>
                                    <div class="stat-info">
                                        <div class="stat-number">20+</div>
                                        <div class="stat-label">Bác sĩ thú y</div>
                                    </div>
                                </div>
                                <div class="stat-card" data-aos="zoom-in" data-aos-delay="600">
                                    <div class="stat-icon">
                                        <i class="fas fa-paw"></i>
                                    </div>
                                    <div class="stat-info">
                                        <div class="stat-number">5.000+</div>
                                        <div class="stat-label">Thú cưng được chăm sóc</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="stats-section">
            <div class="container">
                <div class="stats-container" data-aos="fade-up">
                    <div class="row g-0">
                        <div class="col-lg-3 col-md-6">
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number" data-count="10">0</div>
                                    <div class="stat-label">Năm chăm sóc thú cưng</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-user-md"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number" data-count="20">0</div>
                                    <div class="stat-label">Bác sĩ thú y</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-paw"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number" data-count="5000">0</div>
                                    <div class="stat-label">Thú cưng được chăm sóc</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number" data-count="98">0</div>
                                    <div class="stat-label">% Khách hàng hài lòng</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Mission Section -->
        <section id="mission" class="mission-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                        <div class="section-header">
                            <span class="section-badge">Sứ mệnh</span>
                            <h2 class="section-title">
                                Cam kết mang đến
                                <span class="text-gradient">dịch vụ tốt nhất cho thú cưng</span>
                            </h2>
                            <p class="section-description">
                                Với phương châm "Thú cưng là thành viên trong gia đình", chúng tôi luôn nỗ lực
                                mang đến dịch vụ chăm sóc, điều trị và cung cấp sản phẩm chất lượng cao nhất.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row g-4 mt-4">
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="mission-card">
                            <div class="mission-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="mission-content">
                                <h4>Tận tâm chăm sóc</h4>
                                <p>Luôn đặt sức khỏe và sự an toàn của thú cưng lên hàng đầu trong mọi dịch vụ.</p>
                            </div>
                            <div class="mission-hover">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="mission-card">
                            <div class="mission-icon">
                                <i class="fas fa-microscope"></i>
                            </div>
                            <div class="mission-content">
                                <h4>Công nghệ hiện đại</h4>
                                <p>Ứng dụng thiết bị và công nghệ thú y tiên tiến giúp chẩn đoán chính xác và điều trị hiệu quả.</p>
                            </div>
                            <div class="mission-hover">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="mission-card">
                            <div class="mission-icon">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <div class="mission-content">
                                <h4>Đội ngũ bác sĩ thú y</h4>
                                <p>Đội ngũ bác sĩ thú y giàu kinh nghiệm, tận tâm và yêu thương động vật.</p>
                            </div>
                            <div class="mission-hover">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Values Section -->
        <section class="values-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6" data-aos="fade-right">
                        <div class="values-content">
                            <div class="section-header">
                                <span class="section-badge">Giá trị cốt lõi</span>
                                <h2 class="section-title">
                                    Những giá trị
                                    <span class="text-gradient">định hướng</span>
                                </h2>
                            </div>
                            <div class="values-list">
                                <div class="value-item" data-aos="fade-up" data-aos-delay="100">
                                    <div class="value-icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div class="value-content">
                                        <h4>An toàn tuyệt đối</h4>
                                        <p>Tuân thủ nghiêm ngặt các quy trình thú y, đảm bảo môi trường khám chữa bệnh an toàn cho thú cưng.</p>
                                    </div>
                                </div>
                                <div class="value-item" data-aos="fade-up" data-aos-delay="200">
                                    <div class="value-icon">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="value-content">
                                        <h4>Chất lượng hàng đầu</h4>
                                        <p>Không ngừng nâng cao chất lượng dịch vụ và cung cấp sản phẩm thú cưng chính hãng, uy tín.</p>
                                    </div>
                                </div>
                                <div class="value-item" data-aos="fade-up" data-aos-delay="300">
                                    <div class="value-icon">
                                        <i class="fas fa-handshake"></i>
                                    </div>
                                    <div class="value-content">
                                        <h4>Minh bạch & Tin cậy</h4>
                                        <p>Cung cấp thông tin rõ ràng về dịch vụ, chi phí và quy trình chăm sóc thú cưng, tạo sự an tâm cho khách hàng.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" data-aos="fade-left">
                        <div class="values-visual">
                            <div class="values-image">
                                <img src="assets/images/des_hpt.jpg" alt="Giá trị cốt lõi VetCare Pet" class="img-fluid">
                                <div class="values-overlay">
                                    <div class="values-badge">
                                        <i class="fas fa-paw"></i>
                                        <span>Chăm sóc chuẩn thú y</span>
                                    </div>
                                </div>
                            </div>
                            <div class="values-decoration">
                                <div class="decoration-item decoration-1">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div class="decoration-item decoration-2">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div class="decoration-item decoration-3">
                                    <i class="fas fa-paw"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="team-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                        <div class="section-header">
                            <span class="section-badge">Đội ngũ</span>
                            <h2 class="section-title">
                                Gặp gỡ
                                <span class="text-gradient">đội ngũ bác sĩ thú y</span>
                            </h2>
                            <p class="section-description">
                                Đội ngũ bác sĩ thú y giàu kinh nghiệm, tận tâm và yêu thương động vật,
                                luôn đồng hành cùng bạn trong hành trình chăm sóc sức khỏe cho thú cưng.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row g-4 mt-4">
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="team-card">
                            <div class="team-image">
                                <img src="assets/images/default-avatar.png" alt="BS. Thú y Nguyễn Văn A" class="img-fluid">
                                <div class="team-overlay">
                                    <div class="team-social">
                                        <a href="#" class="social-link">
                                            <i class="fab fa-facebook"></i>
                                        </a>
                                        <a href="#" class="social-link">
                                            <i class="fab fa-linkedin"></i>
                                        </a>
                                        <a href="#" class="social-link">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="team-content">
                                <div class="team-badge">Bác sĩ thú y trưởng</div>
                                <h4>BS. Thú y Nguyễn Văn A</h4>
                                <p class="team-specialty">Chuyên khoa Nội thú y</p>
                                <p class="team-description">Hơn 10 năm kinh nghiệm trong chẩn đoán và điều trị bệnh cho chó, mèo và thú cưng nhỏ.</p>
                                <div class="team-achievements">
                                    <span class="achievement-item">
                                        <i class="fas fa-award"></i>
                                        Bác sĩ thú y xuất sắc
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="team-card">
                            <div class="team-image">
                                <img src="assets/images/default-avatar.png" alt="BS. Thú y Trần Thị B" class="img-fluid">
                                <div class="team-overlay">
                                    <div class="team-social">
                                        <a href="#" class="social-link">
                                            <i class="fab fa-facebook"></i>
                                        </a>
                                        <a href="#" class="social-link">
                                            <i class="fab fa-linkedin"></i>
                                        </a>
                                        <a href="#" class="social-link">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="team-content">
                                <div class="team-badge">Bác sĩ phẫu thuật</div>
                                <h4>BS. Thú y Trần Thị B</h4>
                                <p class="team-specialty">Phẫu thuật & chăm sóc sinh sản</p>
                                <p class="team-description">Chuyên thực hiện các ca phẫu thuật thú y và chăm sóc sinh sản cho thú cưng.</p>
                                <div class="team-achievements">
                                    <span class="achievement-item">
                                        <i class="fas fa-medal"></i>
                                        Chuyên gia phẫu thuật thú y
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="team-card">
                            <div class="team-image">
                                <img src="assets/images/default-avatar.png" alt="BS. Thú y Lê Văn C" class="img-fluid">
                                <div class="team-overlay">
                                    <div class="team-social">
                                        <a href="#" class="social-link">
                                            <i class="fab fa-facebook"></i>
                                        </a>
                                        <a href="#" class="social-link">
                                            <i class="fab fa-linkedin"></i>
                                        </a>
                                        <a href="#" class="social-link">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="team-content">
                                <div class="team-badge">Chuyên gia dinh dưỡng</div>
                                <h4>BS. Thú y Lê Văn C</h4>
                                <p class="team-specialty">Dinh dưỡng & chăm sóc thú cưng</p>
                                <p class="team-description">Tư vấn chế độ dinh dưỡng, chăm sóc và phòng bệnh cho thú cưng ở mọi độ tuổi.</p>
                                <div class="team-achievements">
                                    <span class="achievement-item">
                                        <i class="fas fa-graduation-cap"></i>
                                        Chuyên gia dinh dưỡng thú y
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- History Timeline -->
        <section class="history-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                        <div class="section-header">
                            <span class="section-badge">Hành trình</span>
                            <h2 class="section-title">
                                Hành trình
                                <span class="text-gradient">phát triển VetCare Pet</span>
                            </h2>
                            <p class="section-description">
                                Từ một phòng khám thú cưng nhỏ, VetCare đã không ngừng phát triển
                                trở thành địa chỉ chăm sóc thú cưng uy tín và được hàng ngàn khách hàng tin tưởng.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="timeline-container mt-5">
                    <div class="timeline">

                        <!-- 2009 -->
                        <div class="timeline-item" data-aos="fade-up" data-aos-delay="100">
                            <div class="timeline-marker">
                                <div class="timeline-year">2009</div>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-card">
                                    <div class="timeline-icon">
                                        <i class="fas fa-paw"></i>
                                    </div>
                                    <h4>Khởi đầu VetCare Pet</h4>
                                    <p>
                                        Thành lập phòng khám thú cưng đầu tiên với đội ngũ bác sĩ thú y
                                        và các dịch vụ khám chữa bệnh cơ bản cho chó mèo.
                                    </p>
                                    <div class="timeline-stats">
                                        <span class="stat">3 bác sĩ thú y</span>
                                        <span class="stat">1 phòng khám</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2015 -->
                        <div class="timeline-item" data-aos="fade-up" data-aos-delay="200">
                            <div class="timeline-marker">
                                <div class="timeline-year">2015</div>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-card">
                                    <div class="timeline-icon">
                                        <i class="fas fa-clinic-medical"></i>
                                    </div>
                                    <h4>Mở rộng dịch vụ</h4>
                                    <p>
                                        Phát triển thêm các chi nhánh và mở rộng dịch vụ như spa thú cưng,
                                        tiêm phòng, phẫu thuật và chăm sóc chuyên sâu.
                                    </p>
                                    <div class="timeline-stats">
                                        <span class="stat">15+ nhân sự</span>
                                        <span class="stat">3 chi nhánh</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2020 -->
                        <div class="timeline-item" data-aos="fade-up" data-aos-delay="300">
                            <div class="timeline-marker">
                                <div class="timeline-year">2020</div>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-card">
                                    <div class="timeline-icon">
                                        <i class="fas fa-laptop-medical"></i>
                                    </div>
                                    <h4>Chuyển đổi số</h4>
                                    <p>
                                        Triển khai hệ thống đặt lịch online, quản lý hồ sơ thú cưng
                                        và tư vấn từ xa giúp khách hàng thuận tiện hơn.
                                    </p>
                                    <div class="timeline-stats">
                                        <span class="stat">30+ nhân sự</span>
                                        <span class="stat">Hệ thống online</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2024 -->
                        <div class="timeline-item" data-aos="fade-up" data-aos-delay="400">
                            <div class="timeline-marker">
                                <div class="timeline-year">2024</div>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-card">
                                    <div class="timeline-icon">
                                        <i class="fas fa-award"></i>
                                    </div>
                                    <h4>Phát triển toàn diện</h4>
                                    <p>
                                        Phục vụ hàng chục nghìn thú cưng mỗi năm, cung cấp đầy đủ dịch vụ
                                        từ khám chữa bệnh đến cửa hàng phụ kiện và thức ăn chất lượng cao.
                                    </p>
                                    <div class="timeline-stats">
                                        <span class="stat">50+ nhân sự</span>
                                        <span class="stat">10K+ thú cưng</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-container" data-aos="fade-up">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="cta-content">
                                <h3>Sẵn sàng chăm sóc thú cưng của bạn?</h3>
                                <p>Đặt lịch khám ngay hôm nay để thú cưng của bạn được tư vấn và chăm sóc bởi đội ngũ bác sĩ thú y giàu kinh nghiệm.</p>
                            </div>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <div class="cta-buttons">
                                <a href="appointment.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-calendar-plus me-2"></i>
                                    Đặt lịch khám
                                </a>
                                <a href="contact.php" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-phone me-2"></i>
                                    Liên hệ tư vấn
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Video Modal -->
    <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="videoModalLabel">Giới thiệu VetCare</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="VetCare Introduction" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/about.js"></script>
</body>

</html>