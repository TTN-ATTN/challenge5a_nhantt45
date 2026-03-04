<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang cá nhân - <?= htmlspecialchars($profileUser['full_name']) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .inline-input { padding: 6px; width: 100%; max-width: 250px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
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

        <?php if ($currentUserRole === 'teacher' && $profileUser['role'] === 'student'): ?>
            <form id="editStudentForm" action="/edit-student" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="student_id" value="<?= $profileUser['id'] ?>">
                <input type="hidden" name="current_password" id="edit_current_password" value="">

                <div class="info-group">
                    <strong style="display:inline-block; width: 120px;">Họ và tên:</strong>
                    <span class="view-mode"><?= htmlspecialchars($profileUser['full_name']) ?></span>
                    <input class="edit-mode inline-input" type="text" name="full_name" value="<?= htmlspecialchars($profileUser['full_name']) ?>" style="display: none;" required>
                </div>
                <div class="info-group">
                    <strong style="display:inline-block; width: 120px;">Tên đăng nhập:</strong>
                    <span class="view-mode"><?= htmlspecialchars($profileUser['username']) ?></span>
                    <input class="edit-mode inline-input" type="text" name="username" value="<?= htmlspecialchars($profileUser['username']) ?>" style="display: none;" required>
                </div>
                <div class="info-group">
                    <strong style="display:inline-block; width: 120px;">Email:</strong>
                    <span class="view-mode"><?= htmlspecialchars($profileUser['email']) ?></span>
                    <input class="edit-mode inline-input" type="email" name="email" value="<?= htmlspecialchars($profileUser['email']) ?>" style="display: none;" required>
                </div>
                <div class="info-group">
                    <strong style="display:inline-block; width: 120px;">Số điện thoại:</strong>
                    <span class="view-mode"><?= htmlspecialchars($profileUser['phone_number']) ?></span>
                    <input class="edit-mode inline-input" type="text" name="phone" value="<?= htmlspecialchars($profileUser['phone_number']) ?>" style="display: none;" required>
                </div>
                <div class="info-group edit-mode" style="display: none;">
                    <strong style="display:inline-block; width: 120px;">Mật khẩu mới:</strong>
                    <input class="inline-input" type="password" name="new_password" placeholder="Để trống nếu không đổi">
                </div>
                <div class="info-group view-mode">
                    <strong style="display:inline-block; width: 120px;">Vai trò:</strong> Sinh viên
                </div>

                <hr>
                <div style="margin-top: 20px;">
                    <div id="teacher-view-controls">
                        <button type="button" class="btn" onclick="toggleEditMode(true)">Sửa thông tin sinh viên</button>
                        <button type="button" class="btn btn-danger" onclick="openModal('delete', <?= $profileUser['id'] ?>)">Xóa sinh viên</button>
                    </div>
                    <div id="teacher-edit-controls" style="display: none;">
                        <button type="button" class="btn" style="background: #28a745;" onclick="openModal('edit', <?= $profileUser['id'] ?>)">Lưu thay đổi</button>
                        <button type="button" class="btn" style="background: #6c757d;" onclick="toggleEditMode(false)">Hủy</button>
                    </div>
                </div>
            </form>

        <?php else: ?>
            <div class="info-group"><strong>Họ và tên:</strong> <?= htmlspecialchars($profileUser['full_name']) ?></div>
            <div class="info-group"><strong>Tên đăng nhập:</strong> <?= htmlspecialchars($profileUser['username']) ?></div>
            <div class="info-group"><strong>Email:</strong> <?= htmlspecialchars($profileUser['email']) ?></div>
            <div class="info-group"><strong>Số điện thoại:</strong> <?= htmlspecialchars($profileUser['phone_number']) ?></div>
            <div class="info-group"><strong>Vai trò:</strong> <?= $profileUser['role'] === 'teacher' ? 'Giáo viên' : 'Sinh viên' ?></div>
            <hr>

            <div style="margin-top: 20px;">
                <?php if ($isOwnProfile && $profileUser['role'] === 'student'): ?>
                    <h3>Cập nhật thông tin cá nhân</h3>
                    <form action="/profile" method="POST" enctype="multipart/form-data" style="background: #e9ecef; padding: 15px; border-radius: 5px;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                        <div class="info-group">
                            <label style="color: red; font-weight: bold;">Mật khẩu hiện tại (*):</label><br>
                            <input type="password" name="current_password" required placeholder="Xác thực mật khẩu hiện tại" style="padding:6px; width:100%;">
                        </div>
                        <hr>
                        <div class="info-group">
                            <label>Email mới:</label><br>
                            <input type="email" name="email" value="<?= htmlspecialchars($profileUser['email']) ?>" required style="padding:6px; width:100%;">
                        </div>
                        <div class="info-group">
                            <label>Số điện thoại mới:</label><br>
                            <input type="text" name="phone" value="<?= htmlspecialchars($profileUser['phone_number']) ?>" required style="padding:6px; width:100%;">
                        </div>
                        <div class="info-group">
                            <label>Mật khẩu mới (Để trống nếu không đổi):</label><br>
                            <input type="password" name="new_password" style="padding:6px; width:100%;">
                        </div>
                        <div class="info-group">
                            <label>Avatar URL:</label><br>
                            <input type="text" name="avatar_url" placeholder="http://example.com/image.jpg" style="padding:6px; width:100%;">
                        </div>
                        <div class="info-group">
                            <label>Hoặc Upload File (Max 2MB):</label><br>
                            <input type="file" name="avatar_file" accept="image/png, image/jpeg, image/gif">
                        </div>

                        <button type="submit" class="btn">Lưu thay đổi</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div id="toast-container"></div>

    <form id="deleteStudentForm" action="/delete-student" method="POST" style="display: none;">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <input type="hidden" name="student_id" id="delete_student_id" value="">
        <input type="hidden" name="current_password" id="delete_current_password" value="">
    </form>

    <div id="passwordModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: white; padding: 20px; border-radius: 8px; width: 320px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h3 id="modalTitle" style="margin-top: 0; color: #dc3545;">Xác nhận</h3>
            <p id="modalDesc" style="font-size: 14px; color: #555; margin-bottom: 20px;">Nhập mật khẩu của bạn để xác nhận.</p>

            <input type="password" id="modal_password" style="width: 100%; padding: 10px; margin-bottom: 20px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px;" placeholder="Mật khẩu hiện tại...">

            <div style="display: flex; justify-content: space-between;">
                <button type="button" class="btn" style="background: #6c757d; width: 48%; margin: 0;" onclick="closeModal()">Hủy</button>
                <button type="button" class="btn btn-danger" id="modalConfirmBtn" style="width: 48%; margin: 0;" onclick="submitAction()">Xác nhận</button>
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