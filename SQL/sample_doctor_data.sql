-- Insert sample specialties if not exists
INSERT IGNORE INTO specialties (specialty_id, name, description) VALUES
(1, 'Nội khoa', 'Chuyên khoa nội tổng quát'),
(2, 'Nhi khoa', 'Chuyên khoa nhi'),
(3, 'Tim mạch', 'Chuyên khoa tim mạch'),
(4, 'Da liễu', 'Chuyên khoa da liễu');

-- Insert sample clinics if not exists
INSERT IGNORE INTO clinics (clinic_id, name, address, phone) VALUES
(1, 'Phòng khám số 1', '123 Đường A, Quận 1, TP.HCM', '0901234567'),
(2, 'Phòng khám số 2', '456 Đường B, Quận 2, TP.HCM', '0909876543');

-- Insert sample users if not exists (doctors)
INSERT IGNORE INTO users (user_id, username, email, password, role_id, status) VALUES
(10, 'doctor1', 'doctor1@example.com', MD5('password123'), 2, 'active'),
(11, 'doctor2', 'doctor2@example.com', MD5('password123'), 2, 'active'),
(12, 'doctor3', 'doctor3@example.com', MD5('password123'), 2, 'active');

-- Insert sample doctors
INSERT IGNORE INTO doctors (doctor_id, user_id, specialty_id, clinic_id, biography) VALUES
(1, 10, 1, 1, 'Bác sĩ nội khoa với 10 năm kinh nghiệm'),
(2, 11, 2, 1, 'Bác sĩ nhi khoa với 8 năm kinh nghiệm'),
(3, 12, 3, 2, 'Bác sĩ tim mạch với 12 năm kinh nghiệm');

-- Insert sample schedules
INSERT IGNORE INTO doctor_schedules (doctor_id, clinic_id, day_of_week, start_time, end_time, is_available) VALUES
-- Doctor 1 schedule
(1, 1, 1, '08:00:00', '12:00:00', 1),
(1, 1, 2, '13:30:00', '17:30:00', 1),
(1, 1, 3, '08:00:00', '12:00:00', 1),
(1, 1, 4, '13:30:00', '17:30:00', 1),
(1, 1, 5, '08:00:00', '12:00:00', 1),

-- Doctor 2 schedule
(2, 1, 1, '13:30:00', '17:30:00', 1),
(2, 1, 2, '08:00:00', '12:00:00', 1),
(2, 1, 3, '13:30:00', '17:30:00', 1),
(2, 1, 4, '08:00:00', '12:00:00', 1),
(2, 1, 5, '13:30:00', '17:30:00', 1),

-- Doctor 3 schedule
(3, 2, 1, '08:00:00', '12:00:00', 1),
(3, 2, 2, '08:00:00', '12:00:00', 1),
(3, 2, 3, '13:30:00', '17:30:00', 1),
(3, 2, 4, '13:30:00', '17:30:00', 1),
(3, 2, 5, '08:00:00', '12:00:00', 1);

-- Insert sample off days
INSERT IGNORE INTO doctor_off_days (doctor_id, off_date, reason) VALUES
(1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'Nghỉ phép'),
(1, DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'Đi hội thảo'),
(2, DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'Nghỉ ốm'),
(3, DATE_ADD(CURDATE(), INTERVAL 4 DAY), 'Công tác'); 