<?php
// Start session before any output
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once 'includes/db.php';
require_once 'includes/blog_functions.php';

// Get blog data for homepage
$featured_post = get_featured_post();
$recent_posts = get_recent_posts(4); // Get 4 recent posts for homepage
$categories = get_blog_categories();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VetCare Store - Trang Blog thú cưng</title>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Animate.css CDN for animation -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <!-- Font Awesome 6 CDN for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- SwiperJS CSS for slider -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
  <!-- Custom CSS -->
  <!-- Base CSS -->
  <link rel="stylesheet" href="./assets/css/style.css">
  <link rel="stylesheet" href="./assets/css/index.css">

  <!-- Fallback Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

  <style>
    /* Fallback font settings */
    body {
      font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }

    /* Fix image paths */
    img {
      max-width: 100%;
      height: auto;
    }

    /* Ensure icons display */
    .fas,
    .far,
    .fab {
      font-family: "Font Awesome 6 Free" !important;
    }
  </style>

  <!-- Index Page Header Override -->
  <style>
    /* Force mobile layout on index page */
    @media (max-width: 999.98px) {

      /* Ensure navigation bar is hidden on mobile for index page */
      .nav-bar {
        display: none !important;
      }

      /* Force mobile action area layout on index page */
      .action-area>*:not(.search-toggle-btn):not(.cart-icon) {
        display: none !important;
      }

      .action-area .auth-btn,
      .action-area .login-btn,
      .action-area .appointment-btn,
      .action-area .custom-dropdown,
      .action-area .user-account {
        display: none !important;
      }

      /* Force mobile toggle visibility */
      .mobile-toggle {
        display: flex !important;
      }

      /* Ensure mobile side menu works */
      .mobile-side-menu {
        display: block !important;
      }

      /* Override any hero wrapper interference */
      .hero-header-wrapper .medical-header {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        z-index: 999990 !important;
      }

      .hero-header-wrapper .main-header-content {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 0 0.8rem !important;
        gap: 0.3rem !important;
        flex-wrap: nowrap !important;
        min-height: 55px !important;
        width: 100% !important;
        box-sizing: border-box !important;
      }
    }

    /* Medium screens override for index page */
    @media (min-width: 992px) and (max-width: 999.98px) {
      .nav-bar {
        display: none !important;
      }

      .action-area>*:not(.search-toggle-btn):not(.cart-icon) {
        display: none !important;
      }

      .mobile-toggle {
        display: flex !important;
      }
    }


    /* Button styles */
    .btn-booking:hover,
    .btn-hotline:hover {
      background: rgba(255, 255, 255, 0.95);
      color: #1e40af;
      text-decoration: none;
    }

    .btn-icon {
      width: 50px;
      height: 50px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
      flex-shrink: 0;
      color: #fff;
    }

    .btn-booking .btn-icon,
    .btn-hotline .btn-icon {
      background: linear-gradient(135deg, #3b82f6, #1e40af);
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    /* Override button hover effect */
    .btn::before {
      display: none !important;
    }
  </style>
</head>

<body>
  <?php include 'includes/header.php'; ?>
  <!-- Hero Section -->
  <section class="clinic-hero" style="background: url('./assets/images/main-slider-bg-1-1.png') center/cover no-repeat; min-height: 600px;">
    <div class="container position-relative">
      <div class="hero-header-wrapper" style="padding-top:0;margin-top:-50px;"></div>

      <div class="row align-items-center pt-4 pt-lg-5">

        <div class="col-lg-7 col-12">

          <!-- Badge -->
          <div class="mb-3 animate__animated animate__fadeInLeft animate__delay-1s"
            style="color:#1ec0f7;font-weight:700;letter-spacing:2px;font-size:1.08rem;background:#e3f6fd;display:inline-block;padding:6px 18px 6px 14px;border-radius:15px;box-shadow:0 2px 8px rgba(33,150,243,0.10);">
            CHĂM SÓC THÚ CƯNG 24/7
          </div>

          <!-- Title -->
          <h1 class="clinic-hero-title mb-3 animate__animated animate__fadeInDown animate__faster"
            style="text-align:left;text-shadow:0 2px 12px rgba(33,150,243,0.10);font-size:2.4rem;">
            Chăm Sóc Thú Cưng <span class="highlight">Toàn Diện</span>

            <span class="ms-2 align-middle" style="display:inline-block;vertical-align:middle;">
              <span style="position:relative;display:inline-block;">
                <img src="./assets/images/thumbnail-chuyen-gia-scaled.jpg"
                  alt="video"
                  style="width:70px;height:42px;border-radius:18px;object-fit:cover;">
                <span class="animate__animated animate__pulse animate__infinite"
                  style="position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);">
                  <i class="fas fa-play-circle"
                    style="font-size:1.7rem;color:#1ec0f7;opacity:0.85;"></i>
                </span>
              </span>
            </span>
          </h1>

          <!-- Desc -->
          <div class="clinic-hero-desc mb-3 animate__animated animate__fadeInUp animate__delay-1s"
            style="text-align:left;max-width:470px;font-size:1.08rem;">
            Với đội ngũ bác sĩ thú y giàu kinh nghiệm và trang thiết bị hiện đại,
            chúng tôi mang đến dịch vụ chăm sóc, khám chữa và làm đẹp toàn diện,
            giúp thú cưng của bạn luôn khỏe mạnh và hạnh phúc.
          </div>

          <!-- Button -->
          <a href="services.php"
            class="btn clinic-hero-btn animate__animated animate__fadeInUp animate__delay-2s"
            style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(90deg,#1976d2 0%,#1ec0f7 100%);font-size:1.08rem;padding:12px 32px;box-shadow:0 2px 8px rgba(33,150,243,0.13);">
            Khám Phá Dịch Vụ <i class="fas fa-arrow-right"></i>
          </a>

        </div>

        <!-- RIGHT -->
        <div class="col-lg-5 d-none d-lg-block position-relative animate__animated animate__fadeInRight animate__slower"
          style="min-width:320px;">
          <img src="./assets/images/1.jpeg"
            alt="Doctor"
            style="max-width:320px;max-height:420px;object-fit:contain;">

          <div style="position:absolute;bottom:18px;left:0;background:#fff;border-radius:15px;box-shadow:0 2px 12px rgba(33,150,243,0.10);padding:12px 22px;display:flex;align-items:center;gap:12px;min-width:170px;">
            <span style="font-size:1.4rem;color:#1ec0f7;">
              <i class="fas fa-paw"></i>
            </span>
            <div>
              <div style="font-size:1.08rem;font-weight:700;color:#1976d2;">98%</div>
              <div style="font-size:0.95rem;color:#888;">Khách hàng hài lòng</div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>

  <main style="padding-top: 90px;">
    <!-- About Us -->
    <section id="about" class="section-bg py-5 position-relative overflow-hidden">
      <div class="about-bg"></div>
      <div class="container position-relative">
        <div class="row align-items-center g-5">
          <div class="col-lg-6">
            <div class="position-relative">
              <div class="rotating-images position-relative" style="height: 400px; border-radius: 1rem; overflow: hidden;">
                <img src="./assets/images/thu_y.png" alt="VetCare" class="rotating-image active" style="position: absolute; width: 100%; height: 100%; object-fit: cover; transition: opacity 0.5s;">
                <img src="/assets/images/2.jpeg" alt="VetCare" class="rotating-image" style="position: absolute; width: 100%; height: 100%; object-fit: cover; opacity: 0; transition: opacity 0.5s ease;">
              </div>
              <div class="position-absolute bottom-0 start-0 translate-middle-y" style="left:24px;bottom:-32px;">
                <!-- <div class="experience-box">
                  <div class="experience-number">25<span>+</span></div>
                  <div class="experience-text">Năm kinh nghiệm<br>trong lĩnh vực y tế</div>
                </div> -->
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="text-center mb-5">
              <div class="section-badge-wrapper">
                <span class="section-badge">Về Chúng Tôi</span>
              </div>

              <h2 class="section-title display-5 fw-bold mb-3">
                Chăm Sóc Thú Cưng Toàn Diện
              </h2>

              <div class="rotating-text-wrapper">
                <p class="section-desc rotating-text active">
                  Với nhiều năm kinh nghiệm trong lĩnh vực chăm sóc thú cưng, chúng tôi mang đến các dịch vụ chất lượng cao giúp thú cưng của bạn luôn khỏe mạnh và vui vẻ.
                </p>

                <p class="section-desc rotating-text" style="display: none;">
                  Đội ngũ bác sĩ thú y tận tâm cùng trang thiết bị hiện đại, chúng tôi cung cấp dịch vụ khám chữa bệnh, spa và chăm sóc toàn diện cho thú cưng.
                </p>

                <p class="section-desc rotating-text" style="display: none;">
                  Ngoài ra, VetCare còn cung cấp đa dạng sản phẩm như thức ăn, phụ kiện và đồ chơi, đảm bảo an toàn và phù hợp cho thú cưng của bạn.
                </p>
              </div>
            </div>

            <!-- Stats -->
            <div class="row mb-4 g-3">
              <div class="col-6 col-md-4">
                <div class="stat-box text-center text-md-start animate__animated animate__fadeInLeft">
                  <div class="stat-number">5000+</div>
                  <div class="stat-text">Thú cưng<br>được chăm sóc</div>
                </div>
              </div>

              <div class="col-6 col-md-4">
                <div class="stat-box text-center text-md-start animate__animated animate__fadeInRight">
                  <div class="stat-number">98%</div>
                  <div class="stat-text">Khách hàng<br>hài lòng</div>
                </div>
              </div>

              <div class="col-6 col-md-4">
                <div class="stat-box text-center text-md-start animate__animated animate__fadeInRight">
                  <div class="stat-number">10+</div>
                  <div class="stat-text">Năm<br>kinh nghiệm</div>
                </div>
              </div>
            </div>

            <!-- Button + Founder -->
            <div class="d-flex align-items-center gap-3 flex-wrap">
              <a href="#" class="btn btn-primary btn-lg px-4 py-3 rounded-pill animate__animated animate__fadeInUp">
                <span>Tìm Hiểu Thêm</span>
                <i class="fa-solid fa-arrow-right ms-2"></i>
              </a>

              <div class="founder-box animate__animated animate__fadeInUp">
                <img src="./assets/images/logo.png alt=" VetCare Store" class="rounded-circle">
                <div>
                  <div class="founder-name">VetCare Store</div>
                  <div class="founder-title">Pet Care & Pet Shop</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- Our Services -->
    <section id="services" class="py-5 position-relative overflow-hidden" style="background: linear-gradient(135deg, #1e88e5 0%, #64b5f6 100%);">
      <div class="container position-relative">

        <!-- Header -->
        <div class="text-center mb-5">
          <span class="d-inline-block px-4 py-2 rounded-pill mb-3" style="background: rgba(255,255,255,0.1); color: #fff;">
            Dịch Vụ Thú Cưng
          </span>

          <h2 class="display-4 fw-bold mb-4 text-white">
            Dịch Vụ Của Chúng Tôi
          </h2>

          <p class="text-white-50 mx-auto" style="max-width: 600px;">
            Cung cấp các dịch vụ chăm sóc, làm đẹp và sản phẩm chất lượng cao dành cho thú cưng của bạn
          </p>
        </div>

        <!-- Services -->
        <div class="row g-4">

          <!-- Service 1 -->
          <div class="col-lg-4">
            <div class="card h-100 border-0" style="background: rgba(255,255,255,0.95); border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: 0.3s;">
              <div class="card-body p-4">

                <div class="service-icon-wrapper mb-4 d-flex align-items-center justify-content-center" style="width:80px;height:80px;background:#e3f2fd;border-radius:20px;">
                  <i class="fa-solid fa-stethoscope" style="font-size:32px;color:#1976d2;"></i>
                </div>

                <h4 class="fw-bold mb-2" style="color:#1976d2;">Khám & Điều Trị</h4>

                <p class="text-muted mb-4">
                  Chẩn đoán và điều trị các bệnh lý cho chó mèo với đội ngũ bác sĩ thú y giàu kinh nghiệm.
                </p>

                <div class="service-features mb-4">
                  <div class="mb-2"><i class="fas fa-check text-primary me-2"></i>Khám tổng quát</div>
                  <div class="mb-2"><i class="fas fa-check text-primary me-2"></i>Tiêm phòng</div>
                  <div><i class="fas fa-check text-primary me-2"></i>Điều trị bệnh</div>
                </div>

                <a href="#" class="btn btn-outline-primary rounded-pill w-100">Tìm hiểu thêm</a>

              </div>
            </div>
          </div>

          <!-- Service 2 -->
          <div class="col-lg-4">
            <div class="card h-100 border-0" style="background: rgba(255,255,255,0.95); border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: 0.3s;">
              <div class="card-body p-4">

                <div class="service-icon-wrapper mb-4 d-flex align-items-center justify-content-center" style="width:80px;height:80px;background:#e3f2fd;border-radius:20px;">
                  <i class="fa-solid fa-paw" style="font-size:32px;color:#1976d2;"></i>
                </div>

                <h4 class="fw-bold mb-2" style="color:#1976d2;">Spa & Grooming</h4>

                <p class="text-muted mb-4">
                  Dịch vụ tắm, cắt tỉa lông giúp thú cưng luôn sạch sẽ và đáng yêu.
                </p>

                <div class="service-features mb-4">
                  <div class="mb-2"><i class="fas fa-check text-primary me-2"></i>Tắm & sấy</div>
                  <div class="mb-2"><i class="fas fa-check text-primary me-2"></i>Cắt tỉa lông</div>
                  <div><i class="fas fa-check text-primary me-2"></i>Vệ sinh tai móng</div>
                </div>

                <a href="#" class="btn btn-outline-primary rounded-pill w-100">Tìm hiểu thêm</a>

              </div>
            </div>
          </div>

          <!-- Service 3 -->
          <div class="col-lg-4">
            <div class="card h-100 border-0" style="background: rgba(255,255,255,0.95); border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: 0.3s;">
              <div class="card-body p-4">

                <div class="service-icon-wrapper mb-4 d-flex align-items-center justify-content-center" style="width:80px;height:80px;background:#e3f2fd;border-radius:20px;">
                  <i class="fa-solid fa-cart-shopping" style="font-size:32px;color:#1976d2;"></i>
                </div>

                <h4 class="fw-bold mb-2" style="color:#1976d2;">Pet Shop</h4>

                <p class="text-muted mb-4">
                  Cung cấp thức ăn, phụ kiện và đồ chơi chất lượng cho thú cưng.
                </p>

                <div class="service-features mb-4">
                  <div class="mb-2"><i class="fas fa-check text-primary me-2"></i>Thức ăn dinh dưỡng</div>
                  <div class="mb-2"><i class="fas fa-check text-primary me-2"></i>Phụ kiện</div>
                  <div><i class="fas fa-check text-primary me-2"></i>Đồ chơi</div>
                </div>

                <a href="#" class="btn btn-outline-primary rounded-pill w-100">Tìm hiểu thêm</a>

              </div>
            </div>
          </div>

        </div>

        <!-- Button -->
        <div class="text-center mt-5">
          <a href="services.php" class="btn btn-light btn-lg px-5 py-3 rounded-pill">
            Xem Thêm Dịch Vụ <i class="fa-solid fa-arrow-right ms-2"></i>
          </a>
        </div>

      </div>

      <!-- Hover effect -->
      <style>
        .card:hover {
          transform: translateY(-8px);
        }
      </style>

    </section>

    <!-- Health Blog Section -->
    <section id="blog" class="py-5">
      <div class="container">

        <!-- Header -->
        <div class="blog-header text-center mb-5">
          <div class="section-badge-wrapper">
            <span class="section-badge">
              <i class="fas fa-paw me-2"></i>Blog Thú Cưng
            </span>
          </div>

          <h2 class="section-title display-4 fw-bold mb-3">
            Góc Thú Cưng
          </h2>

          <div class="section-line mb-4"></div>

          <p class="section-desc mx-auto">
            Chia sẻ kiến thức chăm sóc thú cưng, dinh dưỡng, sức khỏe và kinh nghiệm nuôi thú từ chuyên gia
          </p>
        </div>

        <!-- Categories -->
        <div class="blog-categories mb-4">
          <div class="d-flex flex-wrap justify-content-center gap-2">
            <a href="/blog.php" class="blog-category-badge active">Tất cả</a>

            <?php foreach (array_slice($categories, 0, 8) as $category): ?>
              <a href="/blog.php?category=<?php echo $category['category_id']; ?>" class="blog-category-badge">
                <?php echo htmlspecialchars($category['name']); ?>
              </a>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Content -->
        <div class="row g-4">

          <!-- Left -->
          <div class="col-lg-8">
            <div class="row g-3">

              <?php if ($featured_post): ?>
                <div class="col-12">

                  <div class="blog-banner position-relative rounded-4 overflow-hidden mb-3">

                    <img src="<?php echo htmlspecialchars($featured_post['featured_image']); ?>"
                      class="w-100 h-100 object-fit-cover"
                      style="min-height:220px;max-height:260px;"
                      alt="<?php echo htmlspecialchars($featured_post['title']); ?>">

                    <div class="position-absolute bottom-0 start-0 p-4 w-100"
                      style="background: linear-gradient(0deg,rgba(0,0,0,0.6) 60%,transparent);">

                      <div class="text-white fw-bold" style="font-size:1.2rem;">
                        <?php echo htmlspecialchars($featured_post['title']); ?>
                      </div>

                      <div class="text-white-50 small">
                        <?php echo htmlspecialchars($featured_post['category_name']); ?> •
                        <?php echo date('d/m/Y', strtotime($featured_post['created_at'])); ?>
                      </div>

                    </div>
                  </div>

                </div>

                <div class="col-12">
                  <div class="featured-article bg-white rounded-4 shadow-sm p-4 mb-2">

                    <div class="mb-2 text-primary fw-bold small">
                      <?php echo htmlspecialchars($featured_post['category_name']); ?>
                    </div>

                    <h3 class="mb-2">
                      <?php echo htmlspecialchars($featured_post['title']); ?>
                    </h3>

                    <p class="mb-2 text-muted">
                      <?php echo htmlspecialchars($featured_post['excerpt']); ?>
                    </p>

                    <a href="/blog-post.php?slug=<?php echo $featured_post['slug']; ?>"
                      class="btn btn-link text-primary p-0">
                      Đọc thêm <i class="fas fa-arrow-right ms-2"></i>
                    </a>

                  </div>
                </div>

              <?php else: ?>
                <div class="col-12 text-center py-5">
                  <i class="fas fa-paw text-muted mb-3" style="font-size:3rem;"></i>
                  <h4 class="text-muted">Chưa có bài viết nổi bật</h4>
                  <p class="text-muted">Hãy quay lại sau để xem bài viết mới</p>
                </div>
              <?php endif; ?>

            </div>
          </div>

          <!-- Right -->
          <div class="col-lg-4">
            <div class="d-flex flex-column gap-3">

              <?php if (!empty($recent_posts)): ?>
                <?php foreach ($recent_posts as $post): ?>

                  <div class="mini-article d-flex align-items-center bg-white rounded-4 shadow-sm p-2">

                    <img src="<?php echo htmlspecialchars($post['featured_image']); ?>"
                      class="rounded-3 me-3"
                      style="width:64px;height:64px;object-fit:cover;"
                      alt="<?php echo htmlspecialchars($post['title']); ?>">

                    <div>
                      <div class="text-primary fw-bold small mb-1">
                        <?php echo htmlspecialchars($post['category_name']); ?>
                      </div>

                      <div class="fw-semibold">
                        <a href="/blog-post.php?slug=<?php echo $post['slug']; ?>"
                          class="text-decoration-none text-dark">
                          <?php echo htmlspecialchars($post['title']); ?>
                        </a>
                      </div>

                      <div class="text-muted small mt-1">
                        <i class="far fa-calendar me-1"></i>
                        <?php echo date('d/m/Y', strtotime($post['created_at'])); ?>
                      </div>
                    </div>

                  </div>

                <?php endforeach; ?>
              <?php else: ?>
                <div class="text-center py-4">
                  <i class="fas fa-paw text-muted mb-2" style="font-size:2rem;"></i>
                  <p class="text-muted">Chưa có bài viết mới</p>
                </div>
              <?php endif; ?>

            </div>

            <!-- Button -->
            <div class="text-center mt-4">
              <a href="/blog.php" class="btn btn-primary rounded-pill px-4 py-2">
                <i class="fas fa-paw me-2"></i>
                Xem tất cả bài viết
              </a>
            </div>

          </div>

        </div>

      </div>
    </section>
    <!-- Why Choose Us -->
    <section id="whychoose" class="py-5">
      <div class="container">
        <div class="text-center mb-5">
          <div class="section-badge-wrapper">
            <span class="section-badge">
              <i class="fas fa-paw me-2"></i>Lý Do
            </span>
          </div>
          <h2 class="section-title display-4 fw-bold mb-3">Vì Sao Chọn VetCare</h2>
          <div class="section-line mb-4"></div>
          <p class="section-desc mx-auto">
            Chúng tôi mang đến dịch vụ chăm sóc thú cưng chuyên nghiệp, tận tâm và sản phẩm chất lượng cao
          </p>
        </div>

        <div class="row g-4">

          <!-- Item 1 -->
          <div class="col-lg-3 col-md-6">
            <div class="why-choose-card">
              <div class="card-icon blue">
                <i class="fas fa-user-nurse"></i>
              </div>
              <h3 class="card-title">Đội ngũ chuyên môn</h3>
              <p class="card-desc">Nhân viên giàu kinh nghiệm trong chăm sóc & điều trị thú cưng</p>
              <div class="card-stats">
                <div class="stats-number">10+</div>
                <div class="stats-text">Nhân viên chuyên nghiệp</div>
              </div>
            </div>
          </div>

          <!-- Item 2 -->
          <div class="col-lg-3 col-md-6">
            <div class="why-choose-card">
              <div class="card-icon green">
                <i class="fas fa-shower"></i>
              </div>
              <h3 class="card-title">Dịch vụ đa dạng</h3>
              <p class="card-desc">Spa, tắm rửa, cắt tỉa lông, khám và điều trị thú cưng</p>
              <div class="card-stats">
                <div class="stats-number">20+</div>
                <div class="stats-text">Dịch vụ chăm sóc</div>
              </div>
            </div>
          </div>

          <!-- Item 3 -->
          <div class="col-lg-3 col-md-6">
            <div class="why-choose-card">
              <div class="card-icon orange">
                <i class="fas fa-shopping-bag"></i>
              </div>
              <h3 class="card-title">Sản phẩm chất lượng</h3>
              <p class="card-desc">Cung cấp thức ăn, phụ kiện và đồ chơi an toàn cho thú cưng</p>
              <div class="card-stats">
                <div class="stats-number">500+</div>
                <div class="stats-text">Sản phẩm đa dạng</div>
              </div>
            </div>
          </div>

          <!-- Item 4 -->
          <div class="col-lg-3 col-md-6">
            <div class="why-choose-card">
              <div class="card-icon pink">
                <i class="fas fa-headset"></i>
              </div>
              <h3 class="card-title">Hỗ trợ tận tâm</h3>
              <p class="card-desc">Tư vấn miễn phí, hỗ trợ chăm sóc thú cưng 24/7</p>
              <div class="card-stats">
                <div class="stats-number">98%</div>
                <div class="stats-text">Khách hàng hài lòng</div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <section id="faqs" class="py-5 position-relative overflow-hidden">
      <div class="faq-bg-pattern"></div>

      <div class="container section-content position-relative">
        <div class="text-center mb-5">
          <div class="section-badge-wrapper">
            <span class="section-badge">FAQs</span>
          </div>
          <h2 class="section-title display-5 fw-bold mb-3">Câu Hỏi Thường Gặp</h2>
          <p class="section-desc">Những thắc mắc phổ biến khi sử dụng dịch vụ & mua sắm tại VetCare</p>
        </div>

        <div class="row justify-content-center">
          <div class="col-lg-10">
            <div class="faq-wrapper">

              <!-- FAQ 1 -->
              <div class="faq-item active">
                <div class="faq-header" data-bs-toggle="collapse" data-bs-target="#faq1">
                  <div class="faq-icon">
                    <i class="fas fa-paw"></i>
                  </div>
                  <div class="faq-question">
                    Làm sao để đặt lịch chăm sóc thú cưng?
                  </div>
                  <div class="faq-toggle">
                    <i class="fas fa-chevron-down"></i>
                  </div>
                </div>

                <div id="faq1" class="faq-body collapse show">
                  <div class="faq-answer">
                    <p>Bạn có thể đặt lịch chăm sóc thú cưng bằng các cách sau:</p>

                    <div class="faq-features">
                      <div class="faq-feature">
                        <div class="feature-icon">
                          <i class="fas fa-globe"></i>
                        </div>
                        <div class="feature-content">
                          <h4>Đặt lịch online</h4>
                          <p>Đặt lịch spa, khám hoặc grooming trực tiếp trên website</p>
                        </div>
                      </div>

                      <div class="faq-feature">
                        <div class="feature-icon">
                          <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="feature-content">
                          <h4>Gọi hotline</h4>
                          <p>Liên hệ <a href="tel:0123456789" class="text-primary">0123 456 789</a> để được tư vấn nhanh</p>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>

              <!-- FAQ 2 -->
              <div class="faq-item">
                <div class="faq-header" data-bs-toggle="collapse" data-bs-target="#faq2">
                  <div class="faq-icon">
                    <i class="fas fa-clock"></i>
                  </div>
                  <div class="faq-question">
                    VetCare có làm việc cuối tuần không?
                  </div>
                  <div class="faq-toggle">
                    <i class="fas fa-chevron-down"></i>
                  </div>
                </div>

                <div id="faq2" class="faq-body collapse">
                  <div class="faq-answer">

                    <div class="working-hours">
                      <div class="hours-item">
                        <div class="day">Thứ 2 - Thứ 6</div>
                        <div class="time">
                          <i class="far fa-clock me-2"></i>
                          8:00 - 20:00
                        </div>
                      </div>

                      <div class="hours-item">
                        <div class="day">Thứ 7</div>
                        <div class="time">
                          <i class="far fa-clock me-2"></i>
                          8:00 - 18:00
                        </div>
                      </div>

                      <div class="hours-item">
                        <div class="day">Chủ nhật</div>
                        <div class="time">
                          <i class="far fa-clock me-2"></i>
                          8:00 - 17:00
                        </div>
                      </div>
                    </div>

                    <div class="emergency-note">
                      <i class="fas fa-info-circle text-primary me-2"></i>
                      Hỗ trợ tư vấn chăm sóc thú cưng online 24/7
                    </div>

                  </div>
                </div>
              </div>

              <!-- FAQ 3 -->
              <div class="faq-item">
                <div class="faq-header" data-bs-toggle="collapse" data-bs-target="#faq3">
                  <div class="faq-icon">
                    <i class="fas fa-credit-card"></i>
                  </div>
                  <div class="faq-question">
                    VetCare hỗ trợ những phương thức thanh toán nào?
                  </div>
                  <div class="faq-toggle">
                    <i class="fas fa-chevron-down"></i>
                  </div>
                </div>

                <div id="faq3" class="faq-body collapse">
                  <div class="faq-answer">

                    <div class="payment-methods">
                      <div class="payment-row">
                        <div class="payment-method">
                          <i class="fas fa-money-bill-wave"></i>
                          <span>Tiền mặt</span>
                        </div>

                        <div class="payment-method">
                          <i class="fas fa-credit-card"></i>
                          <span>Thẻ ngân hàng</span>
                        </div>

                        <div class="payment-method">
                          <i class="fas fa-mobile-alt"></i>
                          <span>Ví điện tử</span>
                        </div>

                        <div class="payment-method">
                          <i class="fas fa-university"></i>
                          <span>Chuyển khoản</span>
                        </div>
                      </div>
                    </div>

                    <div class="payment-note">
                      <i class="fas fa-shield-alt text-success me-2"></i>
                      Thanh toán nhanh chóng & bảo mật tuyệt đối
                    </div>

                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- Commitments -->
        <div class="service-commitments mt-5">
          <div class="row g-4">

            <div class="col-lg-3 col-md-6">
              <div class="commitment-item">
                <div class="commitment-icon">
                  <i class="fas fa-check-circle"></i>
                </div>
                <div class="commitment-content">
                  <h4>Sản phẩm chính hãng</h4>
                  <p>Đảm bảo nguồn gốc rõ ràng, an toàn cho thú cưng</p>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="commitment-item">
                <div class="commitment-icon">
                  <i class="fas fa-undo"></i>
                </div>
                <div class="commitment-content">
                  <h4>Đổi trả linh hoạt</h4>
                  <p>Hỗ trợ đổi trả nếu sản phẩm lỗi hoặc không phù hợp</p>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="commitment-item">
                <div class="commitment-icon">
                  <i class="fas fa-thumbs-up"></i>
                </div>
                <div class="commitment-content">
                  <h4>Cam kết chất lượng</h4>
                  <p>Dịch vụ và sản phẩm đạt tiêu chuẩn cao</p>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="commitment-item">
                <div class="commitment-icon">
                  <i class="fas fa-truck"></i>
                </div>
                <div class="commitment-content">
                  <h4>Giao hàng nhanh</h4>
                  <p>Giao hàng toàn quốc, tiện lợi và nhanh chóng</p>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </section>
  </main>



  <!-- Appointment Modal -->
  <?php include 'includes/appointment-modal.php'; ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- AOS Animation -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

  <!-- Global Enhancements -->
  <script src="assets/js/global-enhancements.js"></script>

  <?php include 'includes/floating_chat.php'; ?>
  <!-- Footer -->
  <?php include 'includes/footer.php'; ?>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const images = document.querySelectorAll('.rotating-image');
      const texts = document.querySelectorAll('.rotating-text');
      let currentIndex = 0;

      function rotateContent() {
        // Hide all images and texts
        images.forEach(img => img.style.opacity = '0');
        texts.forEach(text => {
          text.style.display = 'none';
          text.classList.remove('active');
        });

        // Show current image and text
        images[currentIndex].style.opacity = '1';
        texts[currentIndex].style.display = 'block';
        texts[currentIndex].classList.add('active');

        // Update index
        currentIndex = (currentIndex + 1) % images.length;
      }

      // Initial state
      images[0].style.opacity = '1';
      texts[0].style.display = 'block';
      texts[0].classList.add('active');

      // Rotate every 15 seconds
      setInterval(rotateContent, 5000);

      // Initialize AOS animation
      AOS.init();
    });
  </script>

  <style>
    /* Fix styles for VPS deployment */
    :root {
      --primary-color: #1976d2;
      --secondary-color: #1ec0f7;
      --text-color: #333;
      --light-bg: #f8f9fa;
      --border-color: #e0e0e0;
    }

    /* Ensure proper font loading */
    @font-face {
      font-family: 'Font Awesome 6 Free';
      font-display: swap;
      src: url('./assets/fonts/fa-solid-900.woff2') format('woff2');
    }

    /* Base styles */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      line-height: 1.6;
      color: var(--text-color);
      overflow-x: hidden;
    }

    /* Image optimizations */
    img {
      max-width: 100%;
      height: auto;
      display: block;
    }

    /* Animation fixes */
    .rotating-text.active {
      animation: fadeIn 0.5s ease-in;
      opacity: 1;
      transform: translateY(0);
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .rotating-image {
      transform: scale(1.02);
      transition: transform 0.3s ease, opacity 0.5s ease;
      backface-visibility: hidden;
    }

    .rotating-image.active {
      transform: scale(1);
    }

    /* Fix button styles */
    .btn {
      position: relative;
      overflow: hidden;
      z-index: 1;
    }

    .btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.1);
      transform: translateX(-100%);
      transition: transform 0.3s ease;
      z-index: -1;
    }

    .btn:hover::before {
      transform: translateX(0);
    }

    /* Fix card styles */
    .card {
      backface-visibility: hidden;
      transform: translateZ(0);
      -webkit-font-smoothing: subpixel-antialiased;
    }
  </style>
</body>

</html>