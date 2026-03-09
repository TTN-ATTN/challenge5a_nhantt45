<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Giao Bài & Trả Bài</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body class="bg-gray-100 font-sans p-4 sm:p-6 text-gray-800">
    <div class="max-w-5xl mx-auto">
        <a href="/" class="text-indigo-600 hover:text-indigo-800 font-medium inline-block mb-4 sm:mb-6 transition">&larr; Quay lại trang chủ</a>

        <h2 class="text-2xl sm:text-3xl font-bold text-indigo-600 mb-6 sm:mb-8 border-b pb-2">Hệ Thống Bài Tập</h2>

        <!-- GIÁO VIÊN: FORM GIAO BÀI -->
        <?php if ($currentUser['role'] === 'teacher'): ?>
            <div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm border border-gray-200 mb-6 sm:mb-8">
                <h3 class="text-xl font-bold text-green-600 mb-4">+ Tạo Bài Tập Mới</h3>
                <form action="/assignments/create" method="POST" enctype="multipart/form-data" class="space-y-4 shadow-sm p-4 bg-gray-50 rounded border border-gray-100">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Tiêu đề:</label>
                        <input type="text" name="title" required class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-green-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Mô tả / Hướng dẫn:</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-green-400 focus:outline-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Deadline:</label>
                        <input type="datetime-local" name="deadline" required class="w-full md:w-1/2 px-4 py-2 border rounded focus:ring-2 focus:ring-green-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">File đề bài (pdf, docx, txt, zip, rar - Max 100MB):</label>
                        <input type="file" name="assignment_file" required accept=".pdf,.doc,.docx,.txt,.zip,.rar" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                    </div>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow transition">Giao Bài</button>
                </form>
            </div>
        <?php endif; ?>

        <!-- DANH SÁCH BÀI TẬP -->
        <h3 class="text-2xl font-bold text-gray-700 mb-4">Danh Sách Bài Tập Của Lớp</h3>
        <?php if (empty($assignments)): ?>
            <p class="text-gray-500 bg-white p-6 rounded shadow-sm text-center italic">Hiện chưa có bài tập nào.</p>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($assignments as $hw): ?>
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 relative">
                        <div class="flex flex-col sm:flex-row justify-between sm:items-start mb-4 gap-4">
                            <div>
                                <h4 class="text-xl font-bold text-indigo-700 mb-1"><?= htmlspecialchars($hw['title']) ?></h4>
                                <p class="text-sm text-gray-500">
                                    Giáo viên: <strong class="text-gray-700"><?= htmlspecialchars($hw['teacher_name']) ?></strong> |
                                    Hạn nộp: <strong class="text-red-500"><?= date('d/m/Y H:i', strtotime($hw['deadline'])) ?></strong>
                                </p>
                            </div>
                            <!-- Nút Xóa bài tập cho chính Giáo viên tạo -->
                            <?php if ($currentUser['role'] === 'teacher' && $currentUser['id'] == $hw['teacher_id']): ?>
                                <form action="/assignments/delete" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa bài tập này và toàn bộ bài nộp của sinh viên?');">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                    <input type="hidden" name="assignment_id" value="<?= $hw['id'] ?>">
                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm font-medium py-1.5 px-3 rounded transition shadow-sm">Xóa Bài</button>
                                </form>
                            <?php endif; ?>
                        </div>
                        
                        <div class="text-gray-700 mb-4 bg-gray-50 p-4 rounded text-sm leading-relaxed border border-gray-100">
                            <?= nl2br(htmlspecialchars($hw['description'])) ?>
                        </div>

                        <a href="<?= htmlspecialchars($hw['file_path']) ?>" download class="inline-block bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-bold py-2 px-4 rounded shadow transition mb-4">Tải Đề Bài Về</a>

                        <!-- SINH VIÊN -->
                        <?php if ($currentUser['role'] === 'student'): ?>
                            <div class="mt-4 pt-4 border-t border-gray-200">

                                <!-- Hiển thị bài nộp hiện tại và Điểm -->
                                <?php if (!empty($mySubmissions[$hw['id']])): ?>
                                    <?php
                                    $mySub = $mySubmissions[$hw['id']];
                                    $isLate = strtotime($mySub['created_at']) > strtotime($hw['deadline']);
                                    ?>
                                    <div class="mb-4 p-4 bg-blue-50 rounded-md border-l-4 border-blue-500 shadow-sm">
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center bg-blue-50 gap-2">
                                            <div class="text-sm text-gray-700">
                                                <strong>Trạng thái:</strong> Đã nộp lúc <?= date('d/m/Y H:i', strtotime($mySub['created_at'])) ?>
                                                <?php if ($isLate): ?>
                                                    <span class="bg-red-500 text-white px-2 py-0.5 rounded text-xs ml-2 font-medium">Nộp trễ</span>
                                                <?php else: ?>
                                                    <span class="bg-green-500 text-white px-2 py-0.5 rounded text-xs ml-2 font-medium">Đúng hạn</span>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Nút Gỡ Bài Nộp -->
                                            <form action="/assignments/unsubmit" method="POST" onsubmit="return confirm('Bạn muốn gỡ bài nộp này?');">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                <input type="hidden" name="submission_id" value="<?= $mySub['id'] ?>">
                                                <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white text-xs py-1 px-3 rounded transition">Gỡ bài</button>
                                            </form>
                                        </div>

                                        <div class="mt-2 text-sm">
                                            <strong class="text-gray-700">Điểm số:</strong>
                                            <?php if (isset($mySub['score'])): ?>
                                                <span class="text-red-600 font-bold text-lg ml-1"><?= htmlspecialchars($mySub['score']) ?> / 10</span>
                                            <?php else: ?>
                                                <span class="text-gray-500 italic ml-1">Giáo viên chưa chấm điểm</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Form nộp bài -->
                                <div class="bg-white p-4 border border-gray-200 rounded">
                                    <strong class="block mb-2 text-gray-700"><?= empty($mySubmissions[$hw['id']]) ? 'Nộp bài làm:' : 'Nộp lại bài:' ?></strong>
                                    <form action="/assignments/submit" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                        <input type="hidden" name="assignment_id" value="<?= $hw['id'] ?>">
                                        <input type="file" name="submission_file" required accept=".pdf,.doc,.docx,.txt,.zip,.rar" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded shadow transition whitespace-nowrap"><?= empty($mySubmissions[$hw['id']]) ? 'Nộp Bài' : 'Nộp Lại' ?></button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- GIÁO VIÊN -->
                        <?php if ($currentUser['role'] === 'teacher'): ?>
                            <div class="mt-6 bg-gray-50 border-l-4 border-green-500 p-4 rounded">
                                <strong class="text-gray-700 block mb-2">Danh sách nộp bài (<?= count($submissions[$hw['id']] ?? []) ?>):</strong>
                                <?php if (empty($submissions[$hw['id']])): ?>
                                    <p class="text-sm text-gray-500 italic">Chưa có sinh viên nào nộp bài.</p>
                                <?php else: ?>
                                    <ul class="space-y-3">
                                        <?php foreach ($submissions[$hw['id']] as $sub): ?>
                                            <?php $isLate = strtotime($sub['created_at']) > strtotime($hw['deadline']); ?>
                                            <li class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 p-3 bg-white border border-gray-200 rounded shadow-sm">
                                                <div class="flex-1 flex flex-wrap items-center gap-2">
                                                    <strong class="text-indigo-600"><?= htmlspecialchars($sub['student_name']) ?></strong>
                                                    <span class="text-xs text-gray-500">(<?= date('d/m/Y H:i', strtotime($sub['created_at'])) ?>)</span>
                                                    <?php if ($isLate): ?>
                                                        <span class="bg-red-100 text-red-600 px-2 py-0.5 rounded text-xs font-bold">Nộp trễ</span>
                                                    <?php endif; ?>
                                                </div>

                                                <a href="<?= htmlspecialchars($sub['file_path']) ?>" target="_blank" class="text-cyan-600 hover:text-cyan-800 text-sm font-medium hover:underline whitespace-nowrap">Tải bài làm</a>

                                                <!-- Form nhập điểm -->
                                                <form action="/assignments/grade" method="POST" class="flex items-center gap-2">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                    <input type="hidden" name="submission_id" value="<?= $sub['id'] ?>">
                                                    <input type="number" step="0.1" min="0" max="10" name="score" value="<?= $sub['score'] ?? '' ?>" placeholder="Điểm" class="w-16 px-2 py-1 text-sm border rounded focus:ring-2 focus:ring-green-400 focus:outline-none" required>
                                                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white text-xs font-bold py-1.5 px-3 rounded transition shadow-sm">Lưu</button>
                                                </form>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div id="toast-container" class="fixed bottom-4 right-4 z-50 flex flex-col gap-2"></div>
    <script src="/assets/js/script.js"></script>
    <script>
        <?php if (!empty($toastError)): ?> showToast("<?= htmlspecialchars($toastError) ?>", "error");
        <?php endif; ?>
        <?php if (!empty($toastSuccess)): ?> showToast("<?= htmlspecialchars($toastSuccess) ?>", "success");
        <?php endif; ?>
    </script>
</body>

</html>