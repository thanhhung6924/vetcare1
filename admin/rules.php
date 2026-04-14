<?php
session_start();
include '../includes/db.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

// API FastAPI
$api_url = "http://localhost:8000/rules";
$context = stream_context_create(["http" => ["timeout" => 15]]); // Tăng timeout lên một chút cho ổn định
$response = @file_get_contents($api_url, false, $context);
$data = $response ? json_decode($response, true) : null;
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luật gợi ý - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
    <link href="assets/css/sidebar.css" rel="stylesheet">
    <link href="assets/css/header.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/rules.css">

    <style>
        /* 1. Tổng thể nội dung và Layout */
        .main-content {
            background-color: #f4f7f6;
            /* Màu nền xám nhạt cực sang */
        }

        /* 2. Card Style - Đồng bộ với Dashboard */
        .card {
            border: none !important;
            border-radius: 12px !important;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05) !important;
            margin-bottom: 1.5rem !important;
            overflow: hidden;
            background: #fff !important;
        }

        /* Tiêu đề Card cho TREND và CORE */
        .card-header {
            padding: 1.25rem !important;
            border-bottom: 1px solid #f1f4f8 !important;
            background: #fff !important;
        }

        .card-header.bg-warning {
            background: linear-gradient(45deg, #ff9800, #ffc107) !important;
            color: #fff !important;
        }

        .card-header.bg-primary {
            background: linear-gradient(45deg, #4e73df, #224abe) !important;
            color: #fff !important;
        }

        /* 3. Table UI - Làm mới bảng luật */
        .table-responsive {
            border-radius: 0 0 12px 12px;
        }

        .table thead th {
            background-color: #f8fafc !important;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            font-weight: 700;
            color: #64748b;
            padding: 1rem !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }

        .table tbody td {

            vertical-align: middle !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        /* 4. Badges - Tên sản phẩm */
        .badge {
            padding: 0.5rem 0.75rem !important;
            font-weight: 500 !important;
            border-radius: 8px !important;
            font-size: 0.85rem !important;
        }

        /* Màu cho Điều kiện (Antecedents) */
        .bg-info.text-dark {
            background-color: #e0f2fe !important;
            color: #0369a1 !important;
            border: 1px solid #bae6fd !important;
        }

        /* Màu cho Gợi ý (Consequents) */
        .bg-secondary {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
        }

        /* 5. Progress Bar & Chỉ số phân tích */
        .progress-container {
            min-width: 200px;
        }

        .progress-custom {
            height: 8px !important;
            /* Làm thanh progress mảnh mai */
            background-color: #f1f5f9 !important;
            border-radius: 10px !important;
            overflow: hidden;
            margin-bottom: 6px;
        }

        .progress-bar {
            border-radius: 10px;
            background: linear-gradient(90deg, #10b981, #34d399) !important;
        }

        .percent-text {
            font-size: 0.95rem;
            font-weight: 800;
            color: #10b981;
        }

        .stats-detail {
            font-size: 0.75rem !important;
            color: #64748b !important;
            background: #f8fafc;
            padding: 5px 10px;
            border-radius: 6px;
            display: block;
            margin-top: 5px;
            border: 1px solid #f1f5f9;
        }

        .stats-detail b {
            color: #1e293b;
        }

        /* 6. Mũi tên chỉ hướng giữa 2 cột */
        /* .table tbody td:nth-child(2)::after {
            content: "\f105";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: #cbd5e1;
            font-size: 1.2rem;
        } */

        /* 7. Sản phẩm phổ biến (Top bán chạy) */
        .rounded-pill.bg-light {
            background: #fff !important;
            border: 1px solid #e2e8f0 !important;
            color: #475569 !important;
            transition: all 0.2s;
        }

        .rounded-pill.bg-light:hover {
            border-color: #ffc107 !important;
            transform: translateY(-2px);
        }

        /* Nút bấm hành động */
        .page-actions .btn {
            border-radius: 8px !important;
            padding: 0.5rem 1rem !important;
            font-weight: 600 !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05) !important;
        }
    </style>
</head>

<body>
    <div class="admin-wrapper">
        <?php include 'includes/headeradmin.php'; ?>

        <div class="admin-content d-flex"> <?php include 'includes/sidebaradmin.php'; ?>

            <main class="main-content flex-grow-1 p-4">
                <div class="container-fluid">

                    <div class="page-header mb-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h1 class="page-title h3">
                                    Hệ Thống Gợi Ý Thông Minh
                                </h1>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Luật FP-Growth</li>
                                    </ol>
                                </nav>
                            </div>
                            <div class="page-actions">
                                <a href="http://localhost:8000/reload-trend"
                                    class="btn btn-warning btn-sm"
                                    target="hidden_frame"
                                    onclick="setTimeout(() => { window.location.reload(); }, 1000);">
                                    <i class="fas fa-sync-alt me-1"></i>Làm mới Trend
                                </a>

                                <a href="http://localhost:8000/reload-core"
                                    class="btn btn-success btn-sm"
                                    target="hidden_frame"
                                    onclick="setTimeout(() => { window.location.reload(); }, 1500);">
                                    <i class="fas fa-database me-1"></i>Làm mới Core
                                </a>
                            </div>
                        </div>
                    </div>

                    <iframe name="hidden_frame" style="display:none;"></iframe>

                    <?php if (!$data): ?>
                        <div class="alert alert-danger shadow-sm border-start border-4 border-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Lỗi kết nối:</strong> Không thể lấy dữ liệu từ FastAPI (Uvicorn). Hãy đảm bảo server Python đang chạy tại port 8000.
                        </div>
                    <?php else: ?>

                        <div class="card mb-4 shadow-sm border-0">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="m-0 font-weight-bold text-primary">Sản Phẩm Phổ Biến</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2">
                                    <?php
                                    foreach ($data as $key => $value) {
                                        if (strpos($key, 'top_ban_chay') !== false && is_array($value)) {
                                            foreach ($value as $item) {
                                                echo '<span class="badge rounded-pill bg-light text-dark border p-2 px-3 shadow-sm"><i class="fas fa-star text-warning me-1"></i> ' . htmlspecialchars($item) . '</span>';
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <?php
                        $sections = [
                            'TREND' => ['class' => 'warning', 'icon' => 'bolt', 'title' => 'Xu Hướng Ngắn Hạn '],
                            'CORE' => ['class' => 'primary', 'icon' => 'gem', 'title' => 'Dữ Liệu Hệ Thống ']
                        ];

                        foreach ($sections as $type => $config):
                            $targetKey = "";
                            foreach (array_keys($data) as $k) if (strpos($k, $type) !== false) $targetKey = $k;
                            if (!$targetKey) continue;
                            $rules = $data[$targetKey];
                        ?>
                            <div class="card mb-4 shadow-sm border-0">
                                <div class="card-header bg-<?= $config['class'] ?> text-white py-3">
                                    <h6 class="mb-0"><i class="fas fa-<?= $config['icon'] ?> me-2"></i> <?= $config['title'] ?> <span class="badge bg-white text-dark ms-2"><?= count($rules) ?> luật</span></h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 45%">Điều kiện </th>

                                                    <th style="width: 25%">Gợi ý </th>
                                                    <th class="text-center">Chỉ số phân tích</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($rules)): ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center py-4 text-muted">Đang phân tích thêm dữ liệu...</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($rules as $rule): ?>
                                                        <tr>
                                                            <td>
                                                                <?php foreach ($rule['Nếu khách xem (Antecedents)'] as $a): ?>
                                                                    <span class="badge bg-info text-dark fw-normal mb-1"><?= htmlspecialchars($a) ?></span>
                                                                <?php endforeach; ?>
                                                            </td>

                                                            <td>
                                                                <?php foreach ($rule['Gợi ý mua kèm (Consequents)'] as $c): ?>
                                                                    <span class="badge bg-secondary mb-1"><?= htmlspecialchars($c) ?></span>
                                                                <?php endforeach; ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $conf = floatval($rule['Độ tin cậy - Confidence'] ?? 0);
                                                                // Nếu backend trả dạng 0-1 thì nhân 100
                                                                $percent = ($conf <= 1) ? ($conf * 100) : $conf;
                                                                // Chặn lỗi range 0-100
                                                                $percent = max(0, min($percent, 100));
                                                                ?>

                                                                <div class="progress-container">
                                                                    <div class="progress-wrapper">
                                                                        <div class="progress progress-custom">
                                                                            <div class="progress-bar bg-success"
                                                                                role="progressbar"
                                                                                style="width: <?= $percent ?>%;"
                                                                                aria-valuenow="<?= $percent ?>"
                                                                                aria-valuemin="0"
                                                                                aria-valuemax="100">
                                                                            </div>
                                                                        </div>
                                                                        <span class="percent-text">
                                                                            <?= round($percent, 1) ?>%
                                                                        </span>
                                                                    </div>

                                                                    <div class="stats-detail">
                                                                        Support: <b><?= $rule['Độ hỗ trợ - Support'] ?? 0 ?></b> |
                                                                        Lift: <b><?= $rule['Độ nâng - Lift'] ?? 0 ?></b>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin.js"></script>
</body>

</html>