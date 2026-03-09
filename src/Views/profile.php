<?php
$pageTitle = 'Hồ sơ người dùng';
$extraScripts = ['/assets/js/message-handling.js'];
require __DIR__ . '/layout/header.php';
?>
    <div class="max-w-4xl mx-auto">
        

        <div class="bg-white p-8 rounded-lg shadow-sm">
            <h2 class="text-2xl font-bold text-indigo-600 mb-6 border-b pb-2">Thông tin chi tiết</h2>

            <div class="text-center mb-8">
                <?php $avatarUrl = $profileUser['avatar'] ?? '/assets/default-avatar.jpg'; ?>
                <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" class="w-32 h-32 rounded-full object-cover shadow-md mx-auto border-4 border-indigo-50">
            </div>

            <!-- giáo viên -->
            <?php if ($currentUserRole === 'teacher' && $profileUser['role'] === 'student'): ?>
                <form id="editStudentForm" action="/edit-student" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="student_id" value="<?= $profileUser['id'] ?>">
                    <input type="hidden" name="current_password" id="edit_current_password" value="">

                    <div class="flex flex-col md:flex-row md:items-center py-2 border-b border-gray-100 last:border-0">
                        <strong class="w-full md:w-32 text-gray-700">Họ và tên:</strong>
                        <span class="view-mode text-gray-600"><?= htmlspecialchars($profileUser['full_name']) ?></span>
                        <input class="edit-mode hidden w-full md:max-w-xs px-3 py-1.5 border rounded focus:ring-2 focus:ring-indigo-500 focus:outline-none" type="text" name="full_name" value="<?= htmlspecialchars($profileUser['full_name']) ?>" required>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center py-2 border-b border-gray-100 last:border-0">
                        <strong class="w-full md:w-32 text-gray-700">Tên đăng nhập:</strong>
                        <span class="view-mode text-gray-600"><?= htmlspecialchars($profileUser['username']) ?></span>
                        <input class="edit-mode hidden w-full md:max-w-xs px-3 py-1.5 border rounded focus:ring-2 focus:ring-indigo-500 focus:outline-none" type="text" name="username" value="<?= htmlspecialchars($profileUser['username']) ?>" required>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center py-2 border-b border-gray-100 last:border-0">
                        <strong class="w-full md:w-32 text-gray-700">Email:</strong>
                        <span class="view-mode text-gray-600"><?= htmlspecialchars($profileUser['email']) ?></span>
                        <input class="edit-mode hidden w-full md:max-w-xs px-3 py-1.5 border rounded focus:ring-2 focus:ring-indigo-500 focus:outline-none" type="email" name="email" value="<?= htmlspecialchars($profileUser['email']) ?>" required>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center py-2 border-b border-gray-100 last:border-0">
                        <strong class="w-full md:w-32 text-gray-700">Số điện thoại:</strong>
                        <span class="view-mode text-gray-600"><?= htmlspecialchars($profileUser['phone_number']) ?></span>
                        <input class="edit-mode hidden w-full md:max-w-xs px-3 py-1.5 border rounded focus:ring-2 focus:ring-indigo-500 focus:outline-none" type="text" name="phone" value="<?= htmlspecialchars($profileUser['phone_number']) ?>" required>
                    </div>
                    <div class="edit-mode hidden flex-col md:flex-row md:items-center py-2 border-b border-gray-100 last:border-0">
                        <strong class="w-full md:w-32 text-gray-700">Mật khẩu mới:</strong>
                        <input class="w-full md:max-w-xs px-3 py-1.5 border rounded focus:ring-2 focus:ring-indigo-500 focus:outline-none" type="password" name="new_password" placeholder="Để trống nếu không đổi">
                    </div>
                    <div class="view-mode flex flex-col md:flex-row md:items-center py-2 border-b border-gray-100 last:border-0">
                        <strong class="w-full md:w-32 text-gray-700">Vai trò:</strong> 
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700 font-medium">Sinh viên</span>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <div id="teacher-view-controls" class="flex gap-4">
                            <button type="button" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded shadow transition" onclick="toggleEditMode(true)">Sửa thông tin sinh viên</button>
                            <button type="button" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded shadow transition" onclick="openModal('delete', <?= $profileUser['id'] ?>)">Xóa sinh viên</button>
                        </div>
                        <div id="teacher-edit-controls" class="hidden flex gap-4">
                            <button type="button" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded shadow transition" onclick="openModal('edit', <?= $profileUser['id'] ?>)">Lưu thay đổi</button>
                            <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded shadow transition" onclick="toggleEditMode(false)">Hủy</button>
                        </div>
                    </div>
                </form>

            <!-- hiển thị thông tin -->
            <?php else: ?>
                <div class="space-y-3">
                    <div class="flex flex-col sm:flex-row border-b border-gray-100 py-2"><strong class="w-32 text-gray-700">Họ và tên:</strong> <span class="text-gray-600"><?= htmlspecialchars($profileUser['full_name']) ?></span></div>
                    <div class="flex flex-col sm:flex-row border-b border-gray-100 py-2"><strong class="w-32 text-gray-700">Tên đăng nhập:</strong> <span class="text-gray-600"><?= htmlspecialchars($profileUser['username']) ?></span></div>
                    <div class="flex flex-col sm:flex-row border-b border-gray-100 py-2"><strong class="w-32 text-gray-700">Email:</strong> <span class="text-gray-600"><?= htmlspecialchars($profileUser['email']) ?></span></div>
                    <div class="flex flex-col sm:flex-row border-b border-gray-100 py-2"><strong class="w-32 text-gray-700">Số điện thoại:</strong> <span class="text-gray-600"><?= htmlspecialchars($profileUser['phone_number']) ?></span></div>
                    <div class="flex flex-col sm:flex-row py-2"><strong class="w-32 text-gray-700">Vai trò:</strong> 
                        <span class="px-2 py-1 text-xs rounded-full <?= $profileUser['role'] === 'teacher' ? 'bg-indigo-100 text-indigo-700' : 'bg-blue-100 text-blue-700' ?> font-medium">
                            <?= $profileUser['role'] === 'teacher' ? 'Giáo viên' : 'Sinh viên' ?>
                        </span>
                    </div>
                </div>

                <div class="mt-8">
                    <!-- cập nhật thông tin -->
                    <?php if ($isOwnProfile && $profileUser['role'] === 'student'): ?>
                        <h3 class="text-xl font-bold text-gray-700 mb-4 border-t pt-6">Cập nhật thông tin cá nhân</h3>
                        <form action="/profile" method="POST" enctype="multipart/form-data" class="bg-gray-50 p-6 rounded-lg border border-gray-200 shadow-sm space-y-4">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                            <div>
                                <label class="block text-red-600 font-bold mb-1">Mật khẩu hiện tại (*):</label>
                                <input type="password" name="current_password" required placeholder="Xác thực mật khẩu hiện tại" class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-red-400 focus:outline-none">
                            </div>
                            <hr class="my-4 border-gray-300">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Email mới:</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($profileUser['email']) ?>" required class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Số điện thoại mới:</label>
                                <input type="text" name="phone" value="<?= htmlspecialchars($profileUser['phone_number']) ?>" required class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Mật khẩu mới (Để trống nếu không đổi):</label>
                                <input type="password" name="new_password" class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Avatar URL:</label>
                                <input type="text" name="avatar_url" placeholder="http://example.com/image.jpg" class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Hoặc Upload File (Max 2MB):</label>
                                <input type="file" name="avatar_file" accept="image/png, image/jpeg, image/gif" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>

                            <button type="submit" class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded shadow transition">Lưu thay đổi</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <hr class="my-8 border-t-2 border-dashed border-gray-200">

            <!-- tin nhắn -->
            <?php if (!$isOwnProfile): ?>
                <div class="bg-indigo-50 p-6 rounded-lg border border-indigo-100 mb-8 shadow-sm">
                    <h3 class="text-lg font-bold text-indigo-700 mb-4">Để lại lời nhắn cho <?= htmlspecialchars($profileUser['full_name']) ?></h3>
                    <form action="/send-message" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <input type="hidden" name="receiver_id" value="<?= $profileUser['id'] ?>">
                        <textarea name="content" rows="3" class="w-full p-3 border border-indigo-200 rounded-md focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-y" placeholder="Nhập nội dung tin nhắn..." required></textarea>
                        <button type="submit" class="mt-3 bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-6 rounded shadow transition">Gửi</button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- chỉ hiển thị tin nhắn nếu là chủ nhân trang hoặc người gửi -->
            <div>
                <h3 class="text-xl font-bold text-gray-700 mb-4">(<?= count($messages ?? []) ?> tin nhắn)</h3>
                <?php if (empty($messages)): ?>
                    <p class="text-gray-500 italic bg-gray-50 p-4 rounded-md text-center border border-gray-100">Chưa có tin nhắn nào.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($messages as $msg): ?>
                            <?php if ($isOwnProfile ||  $currentUserId == $msg['sender_id']): ?>
                                <div class="bg-white border border-gray-200 border-l-4 border-l-cyan-500 p-4 rounded-md shadow-sm" id="msg-box-<?= $msg['id'] ?>">
                                    <div class="flex items-center justify-between mb-3 border-b border-gray-100 pb-2">
                                        <div class="flex items-center gap-3">
                                            <?php $senderAvatar = $msg['sender_avatar'] ?? '/assets/default-avatar.jpg'; ?>
                                            <img src="<?= htmlspecialchars($senderAvatar) ?>" class="w-8 h-8 rounded-full object-cover ring-2 ring-gray-100">
                                            <span class="text-indigo-600 font-bold"><?= htmlspecialchars($msg['sender_name']) ?></span>
                                            <span class="text-xs text-gray-400"><?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?></span>
                                        </div>

                                        <!-- chỉ hiển thị nút sửa/xóa với người gửi -->
                                        <?php if ($currentUserId == $msg['sender_id']): ?>
                                            <div class="flex gap-2">
                                                <button onclick="toggleMsgEdit(<?= $msg['id'] ?>)" class="text-blue-500 hover:text-blue-700 text-sm font-medium transition">Sửa</button>
                                                <form action="/delete-message" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa tin nhắn này?');">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                    <input type="hidden" name="receiver_id" value="<?= $profileUser['id'] ?>">
                                                    <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                                                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium transition">Xóa</button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- xem tin nhắn -->
                                    <div class="text-gray-700 leading-relaxed whitespace-pre-wrap break-words" id="msg-view-<?= $msg['id'] ?>">
                                        <?= nl2br(htmlspecialchars($msg['content'])) ?>
                                    </div>

                                    <!-- sửa tin nhắn (ẩn theo mặc định) -->
                                    <?php if ($currentUserId == $msg['sender_id']): ?>
                                        <form id="msg-edit-<?= $msg['id'] ?>" action="/edit-message" method="POST" class="hidden mt-3 bg-gray-50 p-3 rounded border border-gray-200">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                            <input type="hidden" name="receiver_id" value="<?= $profileUser['id'] ?>">
                                            <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                                            <textarea name="content" rows="3" class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-400 focus:outline-none" required><?= htmlspecialchars($msg['content']) ?></textarea>
                                            <div class="mt-2 text-right space-x-2">
                                                <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm transition" onclick="toggleMsgEdit(<?= $msg['id'] ?>)">Hủy</button>
                                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition">Lưu</button>
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
    </div>

    <?php require __DIR__ . '/layout/footer.php'; ?>


