<?php
$pageTitle = 'Trò Chơi Giải Đố';
$extraScripts = ['/assets/js/chall.js'];
require __DIR__ . '/layout/header.php';
?>
    <div class="max-w-4xl mx-auto">

        <h2 class="text-3xl font-bold text-purple-600 mb-8 border-b pb-2">Trò Chơi Giải Đố</h2>

        <!-- GIÁO VIÊN: TẠO THỬ THÁCH -->
        <?php if ($currentUser['role'] === 'teacher'): ?>
            <div class="bg-purple-50 p-6 rounded-lg shadow-sm border-l-4 border-purple-500 mb-8">
                <h3 class="text-xl font-bold text-purple-700 mb-4">+ Tạo Challenge Mới</h3>
                <form action="/challenges/create" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Gợi ý (Hint):</label>
                        <textarea name="hint" rows="2" class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-purple-400 focus:outline-none" placeholder="Ví dụ: Tên một bài hát của Sơn Tùng M-TP" required></textarea>
                    </div>
                    <div>
                        <label class="block text-red-600 font-bold mb-1">Upload File (.txt):</label>
                        <p class="text-sm text-gray-600 mb-2">Tên file chính là đáp án (viết không dấu, cách nhau khoảng trắng, vd: <strong class="text-gray-800">Em cua ngay hom qua.txt</strong>)</p>
                        <input type="file" name="challenge_file" required accept=".txt" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-purple-100 file:text-purple-700 hover:file:bg-purple-200">
                    </div>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded shadow transition">Tạo Challenge</button>
                </form>
            </div>
        <?php endif; ?>

        <h3 class="text-2xl font-bold text-gray-700 mb-4">Danh Sách Thử Thách</h3>
        <?php if (empty($challenges)): ?>
            <p class="text-gray-500 bg-white p-6 rounded shadow-sm text-center italic">Chưa có thử thách nào.</p>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($challenges as $chall): ?>
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between sm:items-start mb-4 gap-4">
                            <div>
                                <h4 class="text-xl font-bold text-gray-800 mb-1">Challenge #<?= $chall['id'] ?></h4>
                                <p class="text-sm text-gray-500">Giáo viên tạo: <strong class="text-gray-700"><?= htmlspecialchars($chall['teacher_name']) ?></strong> | Ngày: <?= date('d/m/Y', strtotime($chall['created_at'])) ?></p>
                            </div>

                            <!-- Nếu là chủ sở hữu, hiện nút Sửa / Xóa -->
                            <?php if ($currentUser['role'] === 'teacher' && $currentUser['id'] == $chall['teacher_id']): ?>
                                <div class="flex gap-2">
                                    <button onclick="toggleChallEdit(<?= $chall['id'] ?>)" class="bg-cyan-500 hover:bg-cyan-600 text-white text-sm font-medium py-1.5 px-3 rounded shadow-sm transition">Sửa</button>
                                    <form action="/challenges/delete" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa thử thách này?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                        <input type="hidden" name="challenge_id" value="<?= $chall['id'] ?>">
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm font-medium py-1.5 px-3 rounded shadow-sm transition">Xóa</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- xem -->
                        <div id="chall-view-<?= $chall['id'] ?>">
                            <div class="bg-yellow-50 text-yellow-800 p-4 border-l-4 border-yellow-400 rounded-r mb-4">
                                <strong class="font-semibold text-yellow-900">Gợi ý:</strong> <?= nl2br(htmlspecialchars($chall['hint'])) ?>
                            </div>
                        </div>

                        <!-- edit -->
                        <?php if ($currentUser['role'] === 'teacher' && $currentUser['id'] == $chall['teacher_id']): ?>
                            <div id="chall-edit-<?= $chall['id'] ?>" class="hidden bg-gray-50 p-4 rounded border border-gray-300 border-dashed mb-4">
                                <form action="/challenges/edit" method="POST" enctype="multipart/form-data" class="space-y-4">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                    <input type="hidden" name="challenge_id" value="<?= $chall['id'] ?>">

                                    <div>
                                        <label class="block text-gray-700 font-medium mb-1">Cập nhật Gợi ý:</label>
                                        <textarea name="hint" rows="2" class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-green-400 focus:outline-none" required><?= htmlspecialchars($chall['hint']) ?></textarea>
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 font-medium mb-1">Đổi file đáp án mới (.txt) - <i class="text-gray-500">Để trống nếu không muốn đổi</i>:</label>
                                        <input type="file" name="challenge_file" accept=".txt" class="block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300">
                                    </div>

                                    <div class="flex justify-end gap-2">
                                        <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1.5 rounded text-sm transition" onclick="toggleChallEdit(<?= $chall['id'] ?>)">Hủy</button>
                                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded text-sm transition">Lưu Thay Đổi</button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($solvedId) && $solvedId == $chall['id']): ?>
                            <div class="bg-green-100 text-green-800 border border-green-300 p-4 rounded font-mono whitespace-pre-wrap leading-relaxed mt-4">
                                <strong class="text-lg text-green-900 block mb-2">Chúc mừng! Nội dung:</strong>
                                <?= htmlspecialchars($solvedContent) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($currentUser['role'] === 'student'): ?>
                            <form action="/challenges/solve" method="POST" class="mt-4 flex flex-col sm:flex-row gap-2">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <input type="hidden" name="challenge_id" value="<?= $chall['id'] ?>">
                                <input type="text" name="answer" placeholder="Nhập đáp án của bạn..." class="flex-1 px-4 py-2 border rounded focus:ring-2 focus:ring-green-400 focus:outline-none" required>
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow transition whitespace-nowrap">Giải Đố</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php require __DIR__ . '/layout/footer.php'; ?>


