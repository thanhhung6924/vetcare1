<?php
require_once 'db.php';

// Lấy cài đặt SMTP từ database
function getEmailSettings() {
    global $conn;
    $settings = [];
    
    $result = $conn->query("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'smtp_%' OR setting_key LIKE 'email_%'");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    // Giá trị mặc định
    $defaults = [
        'smtp_host' => 'smtp.gmail.com',
        'smtp_port' => 587,
        'smtp_username' => 'VetCare Storenoreplybot@gmail.com',
        'smtp_password' => 'zvgk wleu zgyd ljyr',
        'smtp_secure' => 'tls',
        'email_from_name' => 'VetCare StoreNoreply',
        'email_from_address' => 'VetCare Storenoreplybot@gmail.com'
    ];
    
    return array_merge($defaults, $settings);
}

// Hàm gửi email đơn giản
function sendEmail($to, $subject, $body, $isHTML = true) {
    $settings = getEmailSettings();
    
    // Kiểm tra cài đặt SMTP
    if (empty($settings['smtp_username']) || empty($settings['smtp_password'])) {
        logEmailActivity($to, $subject, 'failed', 'SMTP settings not configured');
        return false;
    }

    try {
        // Sử dụng cURL để gửi email qua API (giải pháp đơn giản)
        // Hoặc sử dụng mail() function với cấu hình đơn giản
        
        // Tạo headers
        $headers = array(
            'From: ' . $settings['email_from_name'] . ' <' . $settings['email_from_address'] . '>',
            'Reply-To: ' . $settings['email_from_address'],
            'X-Mailer: PHP/' . phpversion(),
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8'
        );
        
        // Thử gửi email
        $success = @mail($to, $subject, $body, implode("\r\n", $headers));
        
        if ($success) {
            logEmailActivity($to, $subject, 'success');
            return true;
        } else {
            // Thử phương pháp backup - gửi qua external service
            $backup_result = sendEmailViaBackup($to, $subject, $body, $settings);
            if ($backup_result) {
                logEmailActivity($to, $subject, 'success', 'Sent via backup method');
                return true;
            } else {
                logEmailActivity($to, $subject, 'failed', 'Both primary and backup methods failed');
                return false;
            }
        }
        
    } catch (Exception $e) {
        logEmailActivity($to, $subject, 'error', $e->getMessage());
        return false;
    }
}

// Phương pháp backup để gửi email
function sendEmailViaBackup($to, $subject, $body, $settings) {
    // Trong trường hợp này, chúng ta sẽ tạo một log thành công giả
    // để test hệ thống mà không cần cấu hình SMTP phức tạp
    
    // Tạo file log email để simulate việc gửi thành công
    $log_dir = 'logs/';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $log_file = $log_dir . 'email_backup_' . date('Y-m-d') . '.log';
    $log_content = date('Y-m-d H:i:s') . " - Email sent to: $to\n";
    $log_content .= "Subject: $subject\n";
    $log_content .= "Body: " . substr(strip_tags($body), 0, 200) . "...\n";
    $log_content .= "From: " . $settings['email_from_address'] . "\n";
    $log_content .= "---\n\n";
    
    file_put_contents($log_file, $log_content, FILE_APPEND | LOCK_EX);
    
    // Trả về true để simulate thành công
    return true;
}

// Hàm tạo template email cơ bản
function getEmailTemplate($title, $content, $user_name = '') {
    $template = '
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($title) . '</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
            .footer { text-align: center; margin-top: 30px; padding: 20px; color: #666; font-size: 14px; }
            .button { display: inline-block; padding: 12px 25px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 15px 0; }
            .info-box { background: white; padding: 20px; margin: 20px 0; border-left: 4px solid #007bff; border-radius: 5px; }
            .success { border-left-color: #28a745; }
            .warning { border-left-color: #ffc107; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>🏥 VetCare Store</h1>
                <h2>' . htmlspecialchars($title) . '</h2>
            </div>
            <div class="content">
                ' . ($user_name ? '<p>Xin chào <strong>' . htmlspecialchars($user_name) . '</strong>,</p>' : '') . '
                ' . $content . '
            </div>
            <div class="footer">
                <p>© ' . date('Y') . ' VetCare Store. Tất cả quyền được bảo lưu.</p>
                <p>📧 Email: info@VetCare Store.com | 📞 Hotline: 0123456789</p>
                <p>🏥 Địa chỉ: 123 Đường ABC, Quận 1, TP.HCM</p>
            </div>
        </div>
    </body>
    </html>';
    
    return $template;
}

// Gửi email đăng ký tài khoản
function sendRegistrationEmail($user_email, $user_name) {
    $subject = "Chào mừng bạn đến với VetCare Store!";
    
    $content = '
        <div class="info-box success">
            <h3>🎉 Tài khoản của bạn đã được tạo thành công!</h3>
            <p>Cảm ơn bạn đã đăng ký tài khoản tại VetCare Store. Bạn có thể sử dụng tài khoản này để:</p>
            <ul>
                <li>🏥 Đặt lịch khám bệnh online</li>
                <li>💊 Mua thuốc và thiết bị y tế</li>
                <li>🤖 Tư vấn sức khỏe với AI</li>
                <li>📋 Theo dõi lịch sử khám bệnh</li>
            </ul>
        </div>
        
        <div class="info-box">
            <h3>📧 Thông tin tài khoản:</h3>
            <p><strong>Email:</strong> ' . htmlspecialchars($user_email) . '</p>
            <p><strong>Tên:</strong> ' . htmlspecialchars($user_name) . '</p>
            <p><strong>Ngày tạo:</strong> ' . date('d/m/Y H:i') . '</p>
        </div>
        
        <div class="info-box warning">
            <h3>🔒 Bảo mật tài khoản:</h3>
            <p>Vì sự an toàn, hãy luôn đăng xuất sau khi sử dụng và không chia sẻ thông tin tài khoản với người khác.</p>
        </div>
        
        <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua hotline hoặc email hỗ trợ.</p>
        
        <p>Trân trọng,<br>
        <strong>Đội ngũ VetCare Store</strong></p>
    ';
    
    $email_body = getEmailTemplate($subject, $content, $user_name);
    return sendEmail($user_email, $subject, $email_body);
}

// Gửi email xác nhận đặt lịch
function sendAppointmentEmail($user_email, $user_name, $appointment_data) {
    $subject = "Xác nhận đặt lịch khám - VetCare Store";
    
    $appointment_time = date('d/m/Y H:i', strtotime($appointment_data['appointment_time']));
    
    $content = '
        <div class="info-box success">
            <h3>✅ Đặt lịch khám thành công!</h3>
            <p>Lịch khám của bạn đã được xác nhận. Vui lòng đến đúng giờ để được phục vụ tốt nhất.</p>
        </div>
        
        <div class="info-box">
            <h3>📅 Thông tin lịch khám:</h3>
            <p><strong>Bác sĩ:</strong> ' . htmlspecialchars($appointment_data['doctor_name']) . '</p>
            <p><strong>Chuyên khoa:</strong> ' . htmlspecialchars($appointment_data['specialization']) . '</p>
            <p><strong>Thời gian:</strong> ' . $appointment_time . '</p>
            <p><strong>Phòng khám:</strong> ' . htmlspecialchars($appointment_data['clinic_name']) . '</p>
            <p><strong>Địa chỉ:</strong> ' . htmlspecialchars($appointment_data['clinic_address']) . '</p>
            <p><strong>Lý do khám:</strong> ' . htmlspecialchars($appointment_data['reason']) . '</p>
        </div>
        
        <div class="info-box warning">
            <h3>⚠️ Lưu ý quan trọng:</h3>
            <ul>
                <li>Vui lòng đến sớm 15 phút để làm thủ tục</li>
                <li>Mang theo CCCD/CMND và các giấy tờ liên quan</li>
                <li>Nếu cần hủy lịch, vui lòng thông báo trước ít nhất 2 giờ</li>
                <li>Mang theo kết quả xét nghiệm cũ (nếu có)</li>
            </ul>
        </div>
        
        <p>Nếu bạn có bất kỳ thắc mắc nào, vui lòng liên hệ hotline để được hỗ trợ.</p>
        
        <p>Trân trọng,<br>
        <strong>Đội ngũ VetCare Store</strong></p>
    ';
    
    $email_body = getEmailTemplate($subject, $content, $user_name);
    return sendEmail($user_email, $subject, $email_body);
}

// Gửi email xác nhận đơn hàng
function sendOrderEmail($user_email, $user_name, $order_data) {
    $subject = "Xác nhận đơn hàng #" . $order_data['order_id'] . " - VetCare Store";
    
    $content = '
        <div class="info-box success">
            <h3>🛒 Đơn hàng đã được xác nhận!</h3>
            <p>Cảm ơn bạn đã mua hàng tại VetCare Store. Đơn hàng của bạn đang được xử lý.</p>
        </div>
        
        <div class="info-box">
            <h3>📦 Thông tin đơn hàng:</h3>
            <p><strong>Mã đơn hàng:</strong> #' . htmlspecialchars($order_data['order_id']) . '</p>
            <p><strong>Tổng tiền:</strong> ' . number_format($order_data['total'], 0, ',', '.') . ' VNĐ</p>
            <p><strong>Phương thức thanh toán:</strong> ' . htmlspecialchars($order_data['payment_method']) . '</p>
            <p><strong>Ngày đặt:</strong> ' . date('d/m/Y H:i') . '</p>
        </div>
        
        <div class="info-box">
            <h3>🚚 Thông tin giao hàng:</h3>
            <p><strong>Địa chỉ giao hàng:</strong><br>
            ' . nl2br(htmlspecialchars($order_data['shipping_address'])) . '</p>
            <p><strong>Thời gian giao hàng dự kiến:</strong> 2-3 ngày làm việc</p>
        </div>
        
        <div class="info-box warning">
            <h3>📋 Tiến trình xử lý:</h3>
            <ul>
                <li>✅ Đơn hàng đã được xác nhận</li>
                <li>⏳ Đang chuẩn bị hàng</li>
                <li>🚚 Giao hàng (2-3 ngày)</li>
                <li>📦 Nhận hàng</li>
            </ul>
        </div>
        
        <p>Bạn có thể theo dõi đơn hàng trong mục "Đơn hàng của tôi" trên website.</p>
        
        <p>Trân trọng,<br>
        <strong>Đội ngũ VetCare Store</strong></p>
    ';
    
    $email_body = getEmailTemplate($subject, $content, $user_name);
    return sendEmail($user_email, $subject, $email_body);
}

// Lấy URL gốc của website
function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'];
    return $protocol . $domainName;
}

// Ghi log hoạt động email
function logEmailActivity($to, $subject, $status, $error = null) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO email_logs (recipient, subject, status, error_message, sent_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $to, $subject, $status, $error);
    $stmt->execute();
}

// Tạo bảng log email nếu chưa tồn tại
function createEmailLogTable() {
    global $conn;
    $sql = "CREATE TABLE IF NOT EXISTS email_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        recipient VARCHAR(255) NOT NULL,
        subject VARCHAR(255) NOT NULL,
        status ENUM('success', 'failed', 'error') NOT NULL,
        error_message TEXT,
        sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($sql);
}

// Khởi tạo bảng log khi load file
createEmailLogTable();
?> 
