<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sinh Viên Mới</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <a href="/">&larr; Quay lại trang chủ</a>

    <div class="profile-card" style="margin-top: 20px;">
        <h2 style="color: #28a745;">Thêm Sinh Viên Mới</h2>
        <hr>
        
        <form id="createStudentForm" action="/create-student" method="POST" style="padding: 15px;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="current_password" id="create_current_password" value="">

            <div class="info-group">
                <label>Tên đăng nhập (*):</label><br>
                <input type="text" name="username" required style="padding:8px; width:100%; max-width: 400px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div class="info-group">
                <label>Họ và tên (*):</label><br>
                <input type="text" name="full_name" required style="padding:8px; width:100%; max-width: 400px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div class="info-group">
                <label>Email (*):</label><br>
                <input type="email" name="email" required style="padding:8px; width:100%; max-width: 400px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div class="info-group">
                <label>Số điện thoại (*):</label><br>
                <input type="text" name="phone" required style="padding:8px; width:100%; max-width: 400px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div class="info-group">
                <label>Mật khẩu khởi tạo cho SV (*):</label><br>
                <input type="password" name="password" required style="padding:8px; width:100%; max-width: 400px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <button type="button" class="btn" style="background: #28a745; margin-top: 15px;" onclick="openModal('create', null)">+ Tạo sinh viên</button>
        </form>
    </div>

    <div id="toast-container"></div>

    <div id="passwordModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: white; padding: 20px; border-radius: 8px; width: 320px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h3 id="modalTitle" style="margin-top: 0; color: #28a745;">Xác nhận</h3>
            <p id="modalDesc" style="font-size: 14px; color: #555; margin-bottom: 20px;">Nhập mật khẩu Giáo viên để xác nhận tạo sinh viên.</p>

            <input type="password" id="modal_password" style="width: 100%; padding: 10px; margin-bottom: 20px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px;" placeholder="Mật khẩu hiện tại...">

            <div style="display: flex; justify-content: space-between;">
                <button type="button" class="btn" style="background: #6c757d; width: 48%; margin: 0;" onclick="closeModal()">Hủy</button>
                <button type="button" class="btn" id="modalConfirmBtn" style="background: #28a745; width: 48%; margin: 0;" onclick="submitAction()">Xác nhận</button>
            </div>
        </div>
    </div>

    <script src="/assets/js/script.js"></script>
    <script>
        <?php if (!empty($toastError)): ?>
            showToast("<?= htmlspecialchars($toastError) ?>", "error");
        <?php endif; ?>
        <?php if (!empty($toastSuccess)): ?>
            showToast("<?= htmlspecialchars($toastSuccess) ?>", "success");
        <?php endif; ?>
    </script>
</body>
</html>