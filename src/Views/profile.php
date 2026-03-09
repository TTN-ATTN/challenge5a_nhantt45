<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang cá nhân - <?= htmlspecialchars($profileUser['full_name']) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .inline-input {
            padding: 6px;
            width: 100%;
            max-width: 250px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .message-box {
            background: white;
            border: 1px solid #ddd;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .message-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .message-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .message-sender {
            color: #007bff;
            margin-right: 10px;
            font-weight: bold;
        }

        .message-time {
            font-size: 12px;
            color: #888;
        }

        .message-content {
            color: #333;
            line-height: 1.5;
            white-space: pre-wrap;
            word-break: break-word;
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

        <!-- giáo viên -->
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

            <!-- hiển thị thông tin -->
        <?php else: ?>
            <div class="info-group"><strong>Họ và tên:</strong> <?= htmlspecialchars($profileUser['full_name']) ?></div>
            <div class="info-group"><strong>Tên đăng nhập:</strong> <?= htmlspecialchars($profileUser['username']) ?></div>
            <div class="info-group"><strong>Email:</strong> <?= htmlspecialchars($profileUser['email']) ?></div>
            <div class="info-group"><strong>Số điện thoại:</strong> <?= htmlspecialchars($profileUser['phone_number']) ?></div>
            <div class="info-group"><strong>Vai trò:</strong> <?= $profileUser['role'] === 'teacher' ? 'Giáo viên' : 'Sinh viên' ?></div>
            <hr>

            <div style="margin-top: 20px;">
                <!-- cập nhật thông tin -->
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

        <hr style="margin-top: 30px; border: 0; border-top: 2px dashed #ddd;">

        <!-- tin nhắn -->
        <?php if (!$isOwnProfile): ?>
            <div style="margin-top: 20px; background: #f4f8fa; padding: 20px; border-radius: 8px; border: 1px solid #cde4ee;">
                <h3 style="margin-top: 0; color: #17a2b8;">Để lại lời nhắn cho <?= htmlspecialchars($profileUser['full_name']) ?></h3>
                <form action="/send-message" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="receiver_id" value="<?= $profileUser['id'] ?>">
                    <textarea name="content" rows="3" style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; resize: vertical;" placeholder="Nhập nội dung tin nhắn..." required></textarea>
                    <button type="submit" class="btn" style="margin-top: 10px; background: #17a2b8;">Gửi</button>
                </form>
            </div>
        <?php endif; ?>

        <!-- chỉ hiển thị tin nhắn nếu là chủ nhân trang hoặc người gửi -->
        <div style="margin-top: 20px;">
            <h3 style="color: #333;">(<?= count($messages ?? []) ?> tin nhắn)</h3>
            <?php if (empty($messages)): ?>
                <p style="color: #666; font-style: italic; background: #f9f9f9; padding: 15px; border-radius: 4px; text-align: center;">Chưa có tin nhắn nào.</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column;">
                    <?php foreach ($messages as $msg): ?>
                        <?php if ($isOwnProfile ||  $currentUserId == $msg['sender_id']): ?>
                            <div class="message-box" id="msg-box-<?= $msg['id'] ?>">
                                <div class="message-header" style="justify-content: space-between;">
                                    <div style="display: flex; align-items: center;">
                                        <?php $senderAvatar = $msg['sender_avatar'] ?? '/assets/default-avatar.jpg'; ?>
                                        <img src="<?= htmlspecialchars($senderAvatar) ?>" class="message-avatar">
                                        <span class="message-sender"><?= htmlspecialchars($msg['sender_name']) ?></span>
                                        <span class="message-time"><?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?></span>
                                    </div>

                                    <!-- chỉ hiển thị nút sửa/xóa với người gửi -->
                                    <?php if ($currentUserId == $msg['sender_id']): ?>
                                        <div>
                                            <button onclick="toggleMsgEdit(<?= $msg['id'] ?>)" style="background:none; border:none; color:#007bff; cursor:pointer; font-size:13px;">Sửa</button>
                                            <form action="/delete-message" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa tin nhắn này?');">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                <input type="hidden" name="receiver_id" value="<?= $profileUser['id'] ?>">
                                                <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                                                <button type="submit" style="background:none; border:none; color:#dc3545; cursor:pointer; font-size:13px;">Xóa</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- xem tin nhắn -->
                                <div class="message-content" id="msg-view-<?= $msg['id'] ?>">
                                    <?= nl2br(htmlspecialchars($msg['content'])) ?>
                                </div>

                                <!-- sửa tin nhắn (ẩn theo mặc định) -->
                                <?php if ($currentUserId == $msg['sender_id']): ?>
                                    <form id="msg-edit-<?= $msg['id'] ?>" action="/edit-message" method="POST" style="display: none; margin-top: 10px;">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                        <input type="hidden" name="receiver_id" value="<?= $profileUser['id'] ?>">
                                        <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                                        <textarea name="content" rows="3" style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px;" required><?= htmlspecialchars($msg['content']) ?></textarea>
                                        <div style="margin-top: 5px; text-align: right;">
                                            <button type="button" class="btn" style="background: #6c757d; padding: 4px 8px; font-size: 13px;" onclick="toggleMsgEdit(<?= $msg['id'] ?>)">Hủy</button>
                                            <button type="submit" class="btn" style="background: #28a745; padding: 4px 8px; font-size: 13px;">Lưu</button>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

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
    <script src="/assets/js/message-handling.js"></script>
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