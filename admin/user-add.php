<?php
session_start();
require_once '../includes/db.php';

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role_id = (int)$_POST['role_id'];
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $status = $_POST['status'];
    
    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Vui lòng điền đầy đủ thông tin bắt buộc!';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không khớp!';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự!';
    } else {
        // Kiểm tra username và email đã tồn tại
        $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        if ($check_stmt) {
            $check_stmt->bind_param("ss", $username, $email);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = 'Tên đăng nhập hoặc email đã tồn tại!';
            } else {
                // Thêm user mới
                $conn->begin_transaction();
                try {
                    // Insert user
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $user_stmt = $conn->prepare("INSERT INTO users (username, email, password, role_id, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                    $user_stmt->bind_param("sssis", $username, $email, $hashed_password, $role_id, $status);
                    $user_stmt->execute();
                    
                    $user_id = $conn->insert_id;
                    
                    // Insert user info
                    if (!empty($full_name) || !empty($phone) || !empty($address) || !empty($date_of_birth)) {
                        $info_stmt = $conn->prepare("INSERT INTO users_info (user_id, full_name, phone, address, date_of_birth, gender, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                        $info_stmt->bind_param("isssss", $user_id, $full_name, $phone, $address, $date_of_birth, $gender);
                        $info_stmt->execute();
                    }
                    
                    $conn->commit();
                    header('Location: users.php?success=added');
                    exit;
                } catch (Exception $e) {
                    $conn->rollback();
                    $error = 'Có lỗi xảy ra khi thêm người dùng: ' . $e->getMessage();
                }
            }
        } else {
            $error = 'Lỗi hệ thống, vui lòng thử lại!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm người dùng - VetCare Store Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Thêm người dùng mới</h1>
                    <p class="mb-0 text-muted">Tạo tài khoản người dùng mới trong hệ thống</p>
                </div>
                <div>
                    <a href="users.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </a>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-plus me-2"></i>Thông tin người dùng
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="username" 
                                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" 
                                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password" required minlength="6">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="confirm_password" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                                    <select class="form-select" name="role_id" required>
                                        <option value="">Chọn vai trò</option>
                                        <option value="1">Quản trị viên</option>
                                        <option value="2">Bệnh nhân</option>
                                        <option value="3">Bác sĩ</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Họ và tên</label>
                                    <input type="text" class="form-control" name="full_name" 
                                           value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="tel" class="form-control" name="phone" 
                                           value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Địa chỉ</label>
                                    <textarea class="form-control" name="address" rows="3"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Ngày sinh</label>
                                    <input type="date" class="form-control" name="date_of_birth" 
                                           value="<?= htmlspecialchars($_POST['date_of_birth'] ?? '') ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Giới tính</label>
                                    <select class="form-select" name="gender">
                                        <option value="">Chọn giới tính</option>
                                        <option value="male">Nam</option>
                                        <option value="female">Nữ</option>
                                        <option value="other">Khác</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Trạng thái</label>
                                    <select class="form-select" name="status">
                                        <option value="active">Hoạt động</option>
                                        <option value="inactive">Không hoạt động</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="users.php" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Thêm người dùng
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
