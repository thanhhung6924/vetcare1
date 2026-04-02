<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/email_system_simple.php';
require_once 'includes/functions/enhanced_logger.php';

// Redirect nếu đã đăng nhập
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$message = '';
$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Vui lòng nhập địa chỉ email!'
        ]);
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Địa chỉ email không hợp lệ!'
        ]);
        exit();
    }
    
    try {
        // Kiểm tra email có tồn tại trong hệ thống
        $stmt = $conn->prepare("
            SELECT u.user_id, u.username, u.email, ui.full_name 
            FROM users u 
            LEFT JOIN users_info ui ON u.user_id = ui.user_id 
            WHERE u.email = ? 
            LIMIT 1
        ");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (!$user) {
            // Log failed attempt
            EnhancedLogger::logSecurity('PASSWORD_RESET_ATTEMPT', "Failed password reset attempt for non-existent email: {$email}", 'LOW', [
                'email' => $email,
                'ip' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT']
            ]);
            
            echo json_encode([
                'status' => 'error',
                'message' => 'Không tìm thấy tài khoản với địa chỉ email này!'
            ]);
            exit();
        }
        
        // Kiểm tra xem có token nào đang active không (trong vòng 5 phút)
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count 
            FROM password_reset_tokens 
            WHERE email = ? AND expires_at > NOW() AND used = FALSE 
            AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
        ");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $tokenCount = $result->fetch_assoc()['count'];
        
        if ($tokenCount > 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Chúng tôi đã gửi email reset password cho bạn. Vui lòng kiểm tra hộp thư và chờ 5 phút trước khi yêu cầu lại.'
            ]);
            exit();
        }
        
        // Tạo token reset password
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        // Lưu token vào database
        $stmt = $conn->prepare("
            INSERT INTO password_reset_tokens (user_id, email, token, expires_at, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('isssss', 
            $user['user_id'], 
            $email, 
            $token, 
            $expires_at, 
            $_SERVER['REMOTE_ADDR'], 
            $_SERVER['HTTP_USER_AGENT']
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Không thể tạo token reset password');
        }
        
        // Tạo link reset password
        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . $token;
        
        // Tạo nội dung email
        $full_name = $user['full_name'] ?: $user['username'];
        $subject = "Đặt lại mật khẩu - VetCare Store";
        
        $email_body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9;'>
            <div style='background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                <div style='text-align: center; margin-bottom: 30px;'>
                    <h1 style='color: #2563eb; margin: 0;'>
                        <i class='fas fa-stethoscope'></i> VetCare Store
                    </h1>
                    <p style='color: #666; margin: 5px 0 0 0;'>Hệ thống quản lý phòng khám</p>
                </div>
                
                <div style='background-color: #eff6ff; padding: 20px; border-radius: 8px; border-left: 4px solid #2563eb; margin-bottom: 25px;'>
                    <h2 style='color: #1e40af; margin: 0 0 10px 0; font-size: 20px;'>
                        🔐 Yêu cầu đặt lại mật khẩu
                    </h2>
                    <p style='color: #1e40af; margin: 0;'>
                        Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.
                    </p>
                </div>
                
                <div style='margin-bottom: 25px;'>
                    <p style='color: #333; line-height: 1.6; margin: 0 0 15px 0;'>
                        Xin chào <strong>{$full_name}</strong>,
                    </p>
                    <p style='color: #333; line-height: 1.6; margin: 0 0 15px 0;'>
                        Bạn đã yêu cầu đặt lại mật khẩu cho tài khoản <strong>{$user['username']}</strong> 
                        tại hệ thống VetCare Store.
                    </p>
                    <p style='color: #333; line-height: 1.6; margin: 0 0 15px 0;'>
                        Vui lòng nhấp vào nút bên dưới để đặt lại mật khẩu của bạn:
                    </p>
                </div>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$reset_link}' 
                       style='background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); 
                              color: white; 
                              padding: 14px 30px; 
                              text-decoration: none; 
                              border-radius: 8px; 
                              font-weight: 600; 
                              display: inline-block;
                              box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);'>
                        🔑 Đặt lại mật khẩu
                    </a>
                </div>
                
                <div style='background-color: #fef3c7; padding: 15px; border-radius: 8px; border-left: 4px solid #f59e0b; margin: 25px 0;'>
                    <p style='color: #92400e; margin: 0 0 10px 0; font-weight: 600;'>
                        ⚠️ Lưu ý quan trọng:
                    </p>
                    <ul style='color: #92400e; margin: 0; padding-left: 20px; line-height: 1.5;'>
                        <li>Link này chỉ có hiệu lực trong <strong>24 giờ</strong></li>
                        <li>Chỉ sử dụng được <strong>1 lần duy nhất</strong></li>
                        <li>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này</li>
                    </ul>
                </div>
                
                <div style='border-top: 2px solid #e5e7eb; padding-top: 20px; margin-top: 25px;'>
                    <p style='color: #666; font-size: 14px; margin: 0 0 10px 0;'>
                        <strong>Thông tin yêu cầu:</strong>
                    </p>
                    <ul style='color: #666; font-size: 14px; margin: 0; padding-left: 20px; line-height: 1.5;'>
                        <li>Thời gian: " . date('d/m/Y H:i:s') . "</li>
                        <li>IP: " . $_SERVER['REMOTE_ADDR'] . "</li>
                        <li>Trình duyệt: " . substr($_SERVER['HTTP_USER_AGENT'], 0, 50) . "...</li>
                    </ul>
                </div>
                
                <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;'>
                    <p style='color: #666; font-size: 12px; margin: 0;'>
                        © 2025 VetCare Store. Hệ thống quản lý phòng khám.<br>
                        Nếu bạn cần hỗ trợ, vui lòng liên hệ với chúng tôi.
                    </p>
                </div>
            </div>
        </div>";
        
        // Gửi email
        $email_sent = sendEmail($email, $subject, $email_body);
        
        if ($email_sent) {
            // Log successful password reset request
            EnhancedLogger::logSecurity('PASSWORD_RESET_REQUESTED', "Password reset email sent successfully to {$email}", 'MEDIUM', [
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'email' => $email,
                'token_id' => $conn->insert_id,
                'expires_at' => $expires_at
            ]);
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Chúng tôi đã gửi link đặt lại mật khẩu đến email của bạn. Vui lòng kiểm tra hộp thư (bao gồm cả thư mục spam) và làm theo hướng dẫn.'
            ]);
        } else {
            // Log failed email send
            EnhancedLogger::logError('Password reset email failed to send', "Email: {$email}, User: {$user['username']}");
            
            echo json_encode([
                'status' => 'error',
                'message' => 'Không thể gửi email đặt lại mật khẩu. Vui lòng thử lại sau.'
            ]);
        }
        
    } catch (Exception $e) {
        EnhancedLogger::logError('Password reset error', $e->getMessage(), $e->getTraceAsString());
        
        echo json_encode([
            'status' => 'error',
            'message' => 'Đã xảy ra lỗi hệ thống. Vui lòng thử lại sau.'
        ]);
    }
    
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - VetCare Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: url('assets/images/bgk_login_reg.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, 
                rgba(15, 23, 42, 0.7) 0%, 
                rgba(30, 41, 59, 0.8) 50%,
                rgba(51, 65, 85, 0.7) 100%);
            z-index: 1;
        }

        .forgot-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            position: relative;
            z-index: 2;
        }

        .forgot-container {
            width: 100%;
            max-width: 500px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.2),
                0 8px 32px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
            overflow: hidden;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .forgot-header {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            text-align: center;
            padding: 30px 20px;
            position: relative;
        }

        .forgot-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, 
                rgba(239, 68, 68, 0.1) 0%, 
                rgba(220, 38, 38, 0.05) 100%);
        }

        .brand-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            position: relative;
            z-index: 1;
        }

        .brand-icon i {
            font-size: 2.5rem;
            color: white;
        }

        .forgot-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .forgot-header p {
            opacity: 0.9;
            font-size: 1rem;
            font-weight: 400;
            position: relative;
            z-index: 1;
        }

        .forgot-body {
            padding: 40px 30px;
            background: white;
        }

        .info-box {
            background: #fef3c7;
            border: 1px solid #fbbf24;
            border-left: 4px solid #f59e0b;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 28px;
        }

        .info-box h6 {
            color: #92400e;
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .info-box p {
            color: #92400e;
            margin: 0;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            color: #374151;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
            display: block;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 500;
            background: #f9fafb;
            transition: all 0.3s ease;
            color: #1f2937;
        }

        .form-control:focus {
            outline: none;
            border-color: #ef4444;
            background: white;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .form-control::placeholder {
            color: #9ca3af;
            font-weight: 400;
        }

        .form-control.is-invalid {
            border-color: #ef4444;
            background: #fef2f2;
            animation: shake 0.5s ease-in-out;
        }

        .form-control.is-valid {
            border-color: #10b981;
            background: #ecfdf5;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .btn-forgot {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-forgot:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        }

        .btn-forgot:active {
            transform: translateY(0);
        }

        .btn-forgot.loading {
            position: relative;
            color: transparent;
            pointer-events: none;
        }

        .btn-forgot.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .alert {
            border: none;
            border-radius: 12px;
            margin-bottom: 24px;
            padding: 16px 20px;
            font-weight: 500;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            animation: slideIn 0.4s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert i {
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border-left: 4px solid #10b981;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
        }

        .divider {
            text-align: center;
            margin: 24px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e5e7eb;
        }

        .divider span {
            background: white;
            padding: 0 16px;
            color: #6b7280;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .back-link {
            text-align: center;
            padding: 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .back-link:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .back-link p {
            margin: 0;
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .back-link a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            margin-left: 4px;
            transition: color 0.2s ease;
        }

        .back-link a:hover {
            color: #1e40af;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .forgot-wrapper {
                padding: 20px 15px;
            }
            
            .forgot-container {
                max-width: 100%;
                border-radius: 20px;
            }
            
            .forgot-header {
                padding: 35px 25px;
            }
            
            .forgot-body {
                padding: 35px 25px;
            }

            .brand-icon {
                width: 70px;
                height: 70px;
            }

            .brand-icon i {
                font-size: 2.2rem;
            }

            .forgot-header h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="forgot-wrapper">
        <div class="forgot-container">
            <div class="forgot-header">
                <div class="brand-icon">
                    <i class="fas fa-key"></i>
                </div>
                <h1>Quên mật khẩu</h1>
                <p>Đặt lại mật khẩu của bạn</p>
            </div>

            <div class="forgot-body">
                <div class="info-box">
                    <h6><i class="fas fa-info-circle me-2"></i>Hướng dẫn</h6>
                    <p>Nhập địa chỉ email mà bạn đã sử dụng để đăng ký tài khoản. Chúng tôi sẽ gửi link đặt lại mật khẩu đến email của bạn.</p>
                </div>

                <form id="forgotForm">
                    <div class="form-group">
                        <label class="form-label">Địa chỉ email</label>
                        <input type="email" name="email" id="email" class="form-control" 
                               placeholder="Nhập địa chỉ email của bạn" required>
                    </div>

                    <button type="submit" class="btn-forgot" id="forgotBtn">
                        <i class="fas fa-paper-plane me-2"></i>Gửi link đặt lại mật khẩu
                    </button>
                </form>

                <div id="forgotMessage" class="alert" style="display: none;"></div>

                <div class="divider">
                    <span>hoặc</span>
                </div>

                <div class="back-link">
                    <p>Nhớ lại mật khẩu? 
                        <a href="login.php">
                            <i class="fas fa-sign-in-alt me-1"></i>Đăng nhập ngay
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('forgotForm');
            const forgotBtn = document.getElementById('forgotBtn');
            const forgotMessage = document.getElementById('forgotMessage');
            const emailInput = document.getElementById('email');

            // Auto focus email input
            emailInput.focus();

            // Real-time email validation
            emailInput.addEventListener('input', function() {
                const email = this.value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (email && emailRegex.test(email)) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else if (email) {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-valid', 'is-invalid');
                }
            });

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const email = emailInput.value.trim();
                
                if (!email) {
                    showMessage('error', 'Vui lòng nhập địa chỉ email!');
                    emailInput.classList.add('is-invalid');
                    return;
                }

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    showMessage('error', 'Địa chỉ email không hợp lệ!');
                    emailInput.classList.add('is-invalid');
                    return;
                }

                // Show loading state
                forgotBtn.classList.add('loading');
                forgotBtn.disabled = true;
                forgotMessage.style.display = 'none';

                try {
                    const formData = new FormData(form);
                    const response = await fetch('forgot_password.php', {
                        method: 'POST',
                        body: formData
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }

                    const result = await response.json();

                    if (result.status === 'success') {
                        showMessage('success', result.message);
                        emailInput.classList.remove('is-invalid');
                        emailInput.classList.add('is-valid');
                        
                        // Disable form after successful submission
                        setTimeout(() => {
                            emailInput.disabled = true;
                            forgotBtn.innerHTML = '<i class="fas fa-check me-2"></i>Đã gửi email';
                            forgotBtn.disabled = true;
                            forgotBtn.classList.remove('loading');
                        }, 1000);
                    } else {
                        showMessage('error', result.message);
                        emailInput.classList.add('is-invalid');
                    }

                } catch (error) {
                    console.error('Error:', error);
                    showMessage('error', 'Đã xảy ra lỗi kết nối. Vui lòng thử lại sau.');
                } finally {
                    if (!forgotBtn.innerHTML.includes('Đã gửi email')) {
                        forgotBtn.classList.remove('loading');
                        forgotBtn.disabled = false;
                    }
                }
            });

            function showMessage(type, message) {
                forgotMessage.className = `alert alert-${type}`;
                forgotMessage.innerHTML = `
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
                    ${message}
                `;
                forgotMessage.style.display = 'block';
                
                // Auto hide success message after 10 seconds
                if (type === 'success') {
                    setTimeout(() => {
                        forgotMessage.style.display = 'none';
                    }, 10000);
                }
            }
        });
    </script>
</body>
</html> 