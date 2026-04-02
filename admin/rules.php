<?php
session_start();
include '../includes/db.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

// 🔥 API FastAPI
$api_url = "http://localhost:8000/rules";

// Gọi API
$context = stream_context_create([
    "http" => [
        "timeout" => 10
    ]
]);

$response = @file_get_contents($api_url, false, $context);
$data = $response ? json_decode($response, true) : null;
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luật gợi ý - Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
    <link href="assets/css/sidebar.css" rel="stylesheet">
    <link href="assets/css/header.css" rel="stylesheet">
</head>

<body>

    <?php include 'includes/headeradmin.php'; ?>
    <?php include 'includes/sidebaradmin.php'; ?>

    <main class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>📊 Hệ Thống Gợi Ý Thông Minh (FP-Growth)</h2>
                <div class="btn-group">
                    <a href="http://localhost:8000/reload-trend" class="btn btn-outline-warning btn-sm" target="hidden_frame">Làm mới Trend ⚡</a>
                    <a href="http://localhost:8000/reload-core" class="btn btn-outline-success btn-sm" target="hidden_frame">Làm mới Core 💎</a>
                </div>
                <iframe name="hidden_frame" style="display:none;"></iframe>
            </div>

            <?php if (!$data): ?>
                <div class="alert alert-danger shadow-sm">
                    <i class="fas fa-exclamation-triangle"></i> ❌ Không thể kết nối với API FastAPI. Vui lòng kiểm tra server Python (Uvicorn).
                </div>
            <?php else: ?>

                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-fire"></i> Top Sản Phẩm Bán Chạy (Dự phòng)</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <?php
                            // Tìm key chứa 'top_ban_chay'
                            foreach ($data as $key => $value) {
                                if (strpos($key, 'top_ban_chay') !== false) {
                                    foreach ($value as $item) {
                                        echo '<span class="badge rounded-pill bg-light text-dark border p-2 px-3"><i class="fas fa-star text-warning"></i> ' . htmlspecialchars($item) . '</span>';
                                    }
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <?php
                $sections = [
                    'TREND' => ['class' => 'warning', 'icon' => 'bolt', 'title' => 'Xu Hướng Ngắn Hạn (24h)'],
                    'CORE' => ['class' => 'success', 'icon' => 'gem', 'title' => 'Dữ Liệu Hệ Thống (Dài hạn)']
                ];

                foreach ($sections as $type => $config):
                    $targetKey = "";
                    foreach (array_keys($data) as $k) if (strpos($k, $type) !== false) $targetKey = $k;

                    if (!$targetKey) continue;
                    $rules = $data[$targetKey];
                ?>
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-<?= $config['class'] ?> <?= $type == 'CORE' ? 'text-white' : '' ?> py-3">
                            <h5 class="mb-0"><i class="fas fa-<?= $config['icon'] ?>"></i> <?= $config['title'] ?> <small>(<?= count($rules) ?> luật)</small></h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 35%">Nếu khách mua </th>
                                            <th style="width: 10%" class="text-center"></th>
                                            <th style="width: 35%">Gợi ý thêm</th>
                                            <th class="text-center">Chỉ số tin cậy</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($rules)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">Chưa đủ dữ liệu để tạo luật...</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($rules as $rule): ?>
                                                <tr>
                                                    <td>
                                                        <?php foreach ($rule['Nếu khách xem (Antecedents)'] as $a): ?>
                                                            <span class="badge bg-info text-dark fw-normal mb-1"><?= htmlspecialchars($a) ?></span>
                                                        <?php endforeach; ?>
                                                    </td>
                                                    <td class="text-center text-muted"><i class="fas fa-long-arrow-alt-right fa-lg"></i></td>
                                                    <td>
                                                        <?php foreach ($rule['Gợi ý mua kèm (Consequents)'] as $c): ?>
                                                            <span class="badge bg-secondary mb-1"><?= htmlspecialchars($c) ?></span>
                                                        <?php endforeach; ?>
                                                    </td>
                                                    <td><?php

                                                        $conf = $rule['Độ tin cậy - Confidence'] ?? 0;

                                                        // ép kiểu số
                                                        $conf = floatval($conf);

                                                        // nếu backend trả dạng 0-1 thì nhân 100
                                                        if ($conf <= 1) {
                                                            $percent = $conf * 100;
                                                        } else {
                                                            $percent = $conf;
                                                        }

                                                        // chặn lỗi
                                                        $percent = max(0, min($percent, 100));
                                                        ?>


                                                        <div class="d-flex flex-column gap-1">

                                                            <div class="d-flex align-items-center gap-2">

                                                                <div class="progress flex-grow-1" style="height: 14px;">
                                                                    <div class="progress-bar bg-success"
                                                                        style="width: <?= $percent ?>%;">
                                                                    </div>
                                                                </div>

                                                                <span class="fw-bold text-success" style="min-width: 55px;">
                                                                    <?= round($percent, 1) ?>%
                                                                </span>

                                                            </div>

                                                            <small class="text-dark text-center">
                                                                Support: <b><?= $rule['Độ hỗ trợ - Support'] ?? 0 ?></b> |
                                                                Lift: <b><?= $rule['Độ nâng - Lift'] ?? 0 ?></b>
                                                            </small>

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

    <script src="https://kit.fontawesome.com/your-code.js" crossorigin="anonymous"></script>
</body>

</html>