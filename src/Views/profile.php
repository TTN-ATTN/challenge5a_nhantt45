<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang cá nhân - <?= htmlspecialchars($profileUser['full_name']) ?></title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .profile-card {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            background: #f9f9f9;
        }

        .avatar {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ccc;
        }

        .info-group {
            margin-bottom: 10px;
        }

        .btn {
            padding: 8px 12px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-danger {
            background: #dc3545;
        }

        #toast-container {
            visibility: hidden;
            min-width: 250px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 4px;
            padding: 16px;
            position: fixed;
            z-index: 1;
            right: 30px;
            top: 30px;
            font-size: 15px;
            opacity: 0;
            transition: opacity 0.5s, visibility 0.5s;
        }

        #toast-container.show {
            visibility: visible;
            opacity: 1;
        }

        .toast-error {
            background-color: #dc3545 !important;
        }

        .toast-success {
            background-color: #28a745 !important;
        }
    </style>
</head>

<body>
    <a href="/">&larr; Quay lại trang chủ</a>

    <div class="profile-card" style="margin-top: 20px;">
        <h2>Thông tin chi tiết</h2>

        <div style="text-align: center; margin-bottom: 20px;">
            <?php $avatarUrl = $profileUser['avatar'] ?? '/assets/default-avatar.jpg'; ?>
            <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" class="avatar">
        </div>

        <div class="info-group"><strong>Họ và tên:</strong> <?= htmlspecialchars($profileUser['full_name']) ?></div>
        <div class="info-group"><strong>Tên đăng nhập:</strong> <?= htmlspecialchars($profileUser['username']) ?></div>
        <div class="info-group"><strong>Email:</strong> <?= htmlspecialchars($profileUser['email']) ?></div>
        <div class="info-group"><strong>Số điện thoại:</strong> <?= htmlspecialchars($profileUser['phone_number']) ?></div>
        <div class="info-group"><strong>Vai trò:</strong> <?= $profileUser['role'] === 'teacher' ? 'Giáo viên' : 'Sinh viên' ?></div>

        <hr>

        <div style="margin-top: 20px;">
            <?php if ($isOwnProfile && $profileUser['role'] === 'student'): ?>
                <h3>Cập nhật thông tin</h3>
                <form action="/profile" method="POST" enctype="multipart/form-data" style="background: #e9ecef; padding: 15px; border-radius: 5px;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                    <div class="info-group">
                        <label style="color: red; font-weight: bold;">Mật khẩu hiện tại (*):</label><br>
                        <input type="password" name="current_password" required placeholder="Xác thực mật khẩu hiện tại">
                    </div>
                    <hr>
                    <div class="info-group">
                        <label>Email mới:</label><br>
                        <input type="email" name="email" value="<?= htmlspecialchars($profileUser['email']) ?>" required>
                    </div>
                    <div class="info-group">
                        <label>Số điện thoại mới:</label><br>
                        <input type="text" name="phone" value="<?= htmlspecialchars($profileUser['phone_number']) ?>" required>
                    </div>
                    <div class="info-group">
                        <label>Mật khẩu mới (Để trống nếu không đổi):</label><br>
                        <input type="password" name="password">
                    </div>

                    <div class="info-group">
                        <label>Avatar URL:</label><br>
                        <input type="text" name="avatar_url" placeholder="http://example.com/image.jpg">
                    </div>
                    <div class="info-group">
                        <label>Hoặc Upload File (Max 2MB):</label><br>
                        <input type="file" name="avatar_file" accept="image/png, image/jpeg, image/gif">
                    </div>

                    <button type="submit" class="btn">Lưu thay đổi</button>
                </form>

            <?php elseif ($currentUserRole === 'teacher' && $profileUser['role'] === 'student'): ?>
                <button class="btn">Sửa thông tin sinh viên</button>
                <button class="btn btn-danger">Xóa sinh viên</button>
            <?php endif; ?>
        </div>

        <div id="toast-container"></div>

        <script>
            function showToast(message, type) {
                var toast = document.getElementById("toast-container");
                toast.innerText = message;
                // Xóa class cũ
                toast.classList.remove("toast-error", "toast-success");
                // Thêm màu tùy theo loại lỗi hay thành công
                if (type === 'error') toast.classList.add("toast-error");
                if (type === 'success') toast.classList.add("toast-success");
                toast.classList.add("show");
                // Tự động ẩn sau 2 giây
                setTimeout(function() {
                    toast.classList.remove("show");
                }, 2000);
            }

            // Đẩy giá trị từ PHP xuống Javascript
            <?php if (!empty($toastError)): ?>
                showToast("<?= htmlspecialchars($toastError) ?>", "error");
            <?php endif; ?>

            <?php if (!empty($toastSuccess)): ?>
                showToast("<?= htmlspecialchars($toastSuccess) ?>", "success");
            <?php endif; ?>
        </script>
</body>

</html>