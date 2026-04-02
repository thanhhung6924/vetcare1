# VetCare Admin Panel

Hệ thống quản trị toàn diện cho phòng khám VetCare với giao diện hiện đại và tính năng đầy đủ.

## 🚀 Tính năng chính

### 📊 Dashboard

- Thống kê tổng quan hệ thống
- Biểu đồ và báo cáo trực quan
- Thông tin realtime

### 👥 Quản lý người dùng

- Quản lý Admin, Bác sĩ, Bệnh nhân
- Tìm kiếm và lọc theo vai trò
- Phân trang và sắp xếp
- CRUD operations đầy đủ

### 📅 Quản lý lịch hẹn

- Xem danh sách lịch hẹn
- Cập nhật trạng thái (Pending → Confirmed → Completed)
- Lọc theo ngày, trạng thái
- Thống kê nhanh

### 🏥 Quản lý dịch vụ

- Hiển thị dạng grid card đẹp mắt
- Phân loại theo danh mục
- Toggle trạng thái active/inactive
- Upload hình ảnh dịch vụ

### ⚙️ Cài đặt hệ thống

- **Cài đặt chung**: Thông tin website, timezone
- **Đặt lịch hẹn**: Giờ làm việc, khoảng thời gian
- **Email SMTP**: Cấu hình gửi email
- **Sao lưu & Khôi phục**: Backup database
- **Bảo mật**: Session timeout, password policy

## 🎨 Giao diện

### Design System

- **Framework**: Bootstrap 5.3.0
- **Icons**: Font Awesome 6.4.0
- **Fonts**: Inter font family
- **Color Scheme**: Professional blue gradient
- **Layout**: Responsive sidebar + main content

### UI Components

- Modern sidebar navigation
- Collapsible menu items
- Stats cards với animation
- Data tables với pagination
- Toast notifications
- Modal dialogs
- Form validation

### Responsive Design

- **Desktop** (992px+): Full sidebar layout
- **Tablet** (768-992px): Collapsible sidebar
- **Mobile** (<768px): Hidden sidebar với toggle

## 📁 Cấu trúc thư mục

```
admin/
├── assets/
│   ├── css/
│   │   └── admin.css          # Main stylesheet
│   └── js/
│       └── admin.js           # JavaScript functionality
├── includes/
│   ├── header.php            # Header navigation
│   ├── sidebar.php           # Sidebar menu
│   └── footer.php            # Footer
├── index.php                 # Redirect to dashboard
├── dashboard.php             # Main dashboard
├── users.php                 # User management
├── appointments.php          # Appointment management
├── services.php              # Service management
├── config.php                # System settings
└── README.md                 # This file
```

## 🔧 Cài đặt và sử dụng

### Yêu cầu hệ thống

- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx web server
- Bootstrap 5.3.0 (CDN)
- Font Awesome 6.4.0 (CDN)

### Thiết lập

1. Copy thư mục `admin/` vào webroot
2. Đảm bảo kết nối database đã được cấu hình
3. Truy cập `/admin/` sẽ redirect đến `/admin/dashboard.php`

### Đăng nhập

- Chỉ user có `role_id = 1` (Administrator) mới được truy cập
- Tự động redirect về login nếu chưa đăng nhập hoặc không đủ quyền

## 🔐 Bảo mật

### Authentication & Authorization

- Session-based authentication
- Role-based access control (RBAC)
- Automatic logout redirect
- CSRF protection

### Data Security

- Prepared statements cho database queries
- Input validation và sanitization
- XSS protection với htmlspecialchars()
- SQL injection prevention

### Best Practices

- Secure password hashing
- Session security
- File upload validation
- Error handling

## 📱 JavaScript Features

### Core Functionality

- **Sidebar Toggle**: Responsive navigation
- **Search**: Real-time search functionality
- **Form Validation**: Client-side validation
- **AJAX**: Asynchronous form submissions
- **Notifications**: Toast messaging system

### Interactive Elements

- Delete confirmation dialogs
- Loading states for buttons
- Tooltip và popover integration
- Smooth animations
- Auto-refresh capabilities

### Keyboard Shortcuts

- `Ctrl/Cmd + /`: Focus search
- `Escape`: Close modals/dropdowns
- Auto-save form states

## 🎯 Tính năng nâng cao

### Data Management

- **Pagination**: Efficient large dataset handling
- **Sorting**: Column-based sorting
- **Filtering**: Multiple filter criteria
- **Export**: Excel, PDF, CSV export options

### Real-time Updates

- Auto-refresh components
- Real-time notifications
- Live statistics updates
- WebSocket ready architecture

### User Experience

- Breadcrumb navigation
- Loading animations
- Error handling
- Success/error messaging
- Responsive tables

## 🔄 API Integration Ready

Hệ thống được thiết kế để dễ dàng tích hợp API:

```javascript
// Example AJAX form submission
fetch("/admin/api/users.php", {
  method: "POST",
  body: formData,
})
  .then((response) => response.json())
  .then((data) => {
    if (data.success) {
      showToast("Thành công!", "success");
    }
  });
```

## 📊 Database Integration

### Required Tables

- `users` - User accounts
- `users_info` - User profiles
- `roles` - User roles
- `appointments` - Appointment bookings
- `services` - Medical services
- `categories` - Service categories
- `settings` - System configuration

### Database Schema

Tương thích với schema hiện tại của VetCare system.

## 🛠️ Customization

### Theme Customization

CSS variables trong `admin.css`:

```css
:root {
  --primary-color: #0d6efd;
  --sidebar-width: 280px;
  --header-height: 60px;
  --border-radius: 0.375rem;
}
```

### Component Extension

Tất cả components đều modular và có thể extend:

- Thêm menu items trong `sidebar.php`
- Custom dashboard widgets
- Additional form validation
- New table columns

## 📈 Performance Optimization

### Frontend

- Minified CSS/JS
- CDN resources
- Optimized images
- Lazy loading ready

### Backend

- Prepared statements
- Efficient pagination
- Database indexing
- Query optimization

### Caching Strategy

- Browser caching headers
- Static asset optimization
- Database query caching ready

## 🐛 Debugging & Maintenance

### Error Handling

- Comprehensive error messages
- Database transaction rollback
- Graceful failure handling
- User-friendly error pages

### Logging

- User action logging ready
- Error logging capability
- Performance monitoring hooks
- Audit trail system

## 🔮 Future Enhancements

### Planned Features

- [ ] Advanced reporting system
- [ ] Email notification system
- [ ] File upload management
- [ ] Advanced user permissions
- [ ] API documentation
- [ ] Multi-language support
- [ ] Dark mode theme
- [ ] Mobile app integration

### Technical Roadmap

- [ ] PWA capabilities
- [ ] Real-time WebSocket integration
- [ ] Advanced caching layer
- [ ] Microservices architecture
- [ ] Docker containerization

## 📞 Support & Maintenance

### Documentation

- Comprehensive inline comments
- Function documentation
- Database schema documentation
- API endpoint documentation

### Best Practices

- PSR coding standards
- Semantic HTML
- Accessible design
- SEO optimization
- Performance monitoring

---

**Phát triển bởi**: Dalziel Development  
**Phiên bản**: 1.0.0  
**Cập nhật**: 2024  
**License**: Proprietary

Hệ thống admin này được thiết kế để scale và maintain dễ dàng, với architecture hiện đại và user experience tối ưu cho các quản trị viên y tế.
