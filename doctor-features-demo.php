<?php
session_start();
require_once 'includes/db.php';

// Kiểm tra kết nối database
$db_status = $conn ? 'OK' : 'ERROR';

// Lấy danh sách clinic để demo
$clinics = [];
try {
    $stmt = $conn->prepare("SELECT clinic_id, name, address FROM clinics LIMIT 5");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $clinics[] = $row;
        }
        $stmt->close();
    }
} catch (Exception $e) {
    $clinics = [];
}

// Lấy danh sách appointment để demo
$appointments = [];
try {
    $stmt = $conn->prepare("
        SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.status,
               COALESCE(ui.full_name, gu.full_name, u.username) as patient_name
        FROM appointments a
        LEFT JOIN users u ON a.user_id = u.user_id
        LEFT JOIN users_info ui ON a.user_id = ui.user_id
        LEFT JOIN guest_users gu ON a.guest_id = gu.guest_id
        ORDER BY a.appointment_date DESC
        LIMIT 5
    ");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
        $stmt->close();
    }
} catch (Exception $e) {
    $appointments = [];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Tính năng Bác sĩ - VetCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .main-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .feature-card { border: none; border-radius: 15px; transition: all 0.3s; background: #f8f9fa; }
        .feature-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .status-badge { font-size: 0.8rem; }
        .demo-section { border-left: 4px solid #007bff; padding-left: 20px; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="main-card p-5">
            <!-- Header -->
            <div class="text-center mb-5">
                <h1 class="display-4 text-primary mb-3">
                    <i class="fas fa-user-md"></i> Demo Tính năng Bác sĩ
                </h1>
                <p class="lead text-muted">Các tính năng mới đã được thêm vào hệ thống</p>
                <div class="badge bg-success p-2">
                    <i class="fas fa-database"></i> Database: <?= $db_status ?>
                </div>
            </div>

            <div class="row">
                <!-- Tính năng 1: Xem chi tiết lịch hẹn -->
                <div class="col-lg-6 mb-4">
                    <div class="feature-card p-4 h-100">
                        <h3 class="h4 text-primary mb-3">
                            <i class="fas fa-calendar-check"></i> Xem Chi tiết Lịch hẹn
                        </h3>
                        <div class="demo-section mb-3">
                            <h6>✅ Tính năng đã hoàn thành:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i> Tạo file `doctor/appointment-view.php`</li>
                                <li><i class="fas fa-check text-success me-2"></i> Bảo mật: chỉ xem lịch hẹn của bác sĩ hiện tại</li>
                                <li><i class="fas fa-check text-success me-2"></i> Hiển thị đầy đủ thông tin bệnh nhân</li>
                                <li><i class="fas fa-check text-success me-2"></i> Cập nhật trạng thái lịch hẹn</li>
                                <li><i class="fas fa-check text-success me-2"></i> Sửa lỗi role permission AJAX</li>
                            </ul>
                        </div>

                        <div class="mb-3">
                            <h6>📋 Danh sách lịch hẹn mẫu:</h6>
                            <?php if (!empty($appointments)): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Bệnh nhân</th>
                                            <th>Ngày</th>
                                            <th>Trạng thái</th>
                                            <th>Demo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($appointments as $apt): ?>
                                        <tr>
                                            <td>#<?= $apt['appointment_id'] ?></td>
                                            <td><?= htmlspecialchars($apt['patient_name']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($apt['appointment_date'])) ?></td>
                                            <td>
                                                <?php
                                                $status_map = [
                                                    'pending' => 'warning', 'confirmed' => 'success', 
                                                    'completed' => 'info', 'cancelled' => 'danger'
                                                ];
                                                $badge_class = $status_map[$apt['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?= $badge_class ?> status-badge"><?= $apt['status'] ?></span>
                                            </td>
                                            <td>
                                                <a href="doctor/appointment-view.php?id=<?= $apt['appointment_id'] ?>" 
                                                   class="btn btn-outline-primary btn-sm" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-info">Không có dữ liệu lịch hẹn</div>
                            <?php endif; ?>
                        </div>

                        <div class="text-center">
                            <a href="doctor/appointment-view.php?id=9" class="btn btn-primary" target="_blank">
                                <i class="fas fa-external-link-alt"></i> Test Appointment #9
                            </a>
                            <a href="test_doctor_access.php" class="btn btn-outline-info mt-2" target="_blank">
                                <i class="fas fa-bug"></i> Debug & Test
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tính năng 2: Đăng ký nơi làm việc -->
                <div class="col-lg-6 mb-4">
                    <div class="feature-card p-4 h-100">
                        <h3 class="h4 text-primary mb-3">
                            <i class="fas fa-clinic"></i> Đăng ký Nơi làm việc
                        </h3>
                        <div class="demo-section mb-3">
                            <h6>✅ Tính năng đã hoàn thành:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i> Tạo file `doctor/clinic-registration.php`</li>
                                <li><i class="fas fa-check text-success me-2"></i> Hiển thị thông tin bác sĩ hiện tại</li>
                                <li><i class="fas fa-check text-success me-2"></i> Chọn clinic từ danh sách có sẵn</li>
                                <li><i class="fas fa-check text-success me-2"></i> Cập nhật clinic_id trong bảng doctors</li>
                                <li><i class="fas fa-check text-success me-2"></i> UI responsive và thân thiện</li>
                            </ul>
                        </div>

                        <div class="mb-3">
                            <h6>🏥 Danh sách clinic có sẵn:</h6>
                            <?php if (!empty($clinics)): ?>
                            <div class="row">
                                <?php foreach ($clinics as $clinic): ?>
                                <div class="col-12 mb-2">
                                    <div class="border rounded p-2">
                                        <strong><?= htmlspecialchars($clinic['name']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($clinic['address']) ?></small>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-warning">Không có dữ liệu clinic</div>
                            <?php endif; ?>
                        </div>

                        <div class="text-center">
                            <a href="doctor/clinic-registration.php" class="btn btn-success" target="_blank">
                                <i class="fas fa-clinic"></i> Đăng ký Nơi làm việc
                            </a>
                            <a href="test_doctor_access.php" class="btn btn-outline-info mt-2" target="_blank">
                                <i class="fas fa-bug"></i> Debug & Test
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cách sử dụng -->
            <div class="mt-5">
                <div class="feature-card p-4">
                    <h3 class="h4 text-info mb-3">
                        <i class="fas fa-info-circle"></i> Hướng dẫn sử dụng
                    </h3>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>🔍 Xem chi tiết lịch hẹn:</h6>
                            <ol>
                                <li>Đăng nhập với tài khoản bác sĩ (role_id = 2 hoặc 3)</li>
                                <li>Truy cập: <code>doctor/appointment-view.php?id=[appointment_id]</code></li>
                                <li>Chỉ có thể xem lịch hẹn của chính mình</li>
                                <li>Có thể cập nhật trạng thái: xác nhận, hoàn thành, hủy</li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h6>🏥 Đăng ký nơi làm việc:</h6>
                            <ol>
                                <li>Đăng nhập với tài khoản bác sĩ</li>
                                <li>Truy cập: <code>doctor/clinic-registration.php</code></li>
                                <li>Chọn clinic từ danh sách có sẵn</li>
                                <li>Click "Cập nhật Nơi làm việc"</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Links -->
            <div class="text-center mt-4">
                <div class="btn-group" role="group">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-home"></i> Trang chủ
                    </a>
                    <a href="admin/index.php" class="btn btn-outline-primary">
                        <i class="fas fa-cog"></i> Admin
                    </a>
                    <a href="doctor/schedule.php" class="btn btn-outline-success">
                        <i class="fas fa-calendar"></i> Lịch làm việc
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 