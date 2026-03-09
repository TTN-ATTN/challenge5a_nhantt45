<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Giao Bài & Trả Bài</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .card {
            background: white;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .submission-list {
            background: #f9f9f9;
            padding: 10px;
            border-left: 3px solid #28a745;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <a href="/">&larr; Quay lại trang chủ</a>

    <div style="max-width: 800px; margin: 20px auto;">
        <h2 style="color: #007bff;">Hệ Thống Bài Tập</h2>

        <!-- GIÁO VIÊN: FORM GIAO BÀI -->
        <?php if ($currentUser['role'] === 'teacher'): ?>
            <div class="card" style="background: #e9ecef;">
                <h3>+ Tạo Bài Tập Mới</h3>
                <form action="/assignments/create" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                    <div style="margin-bottom: 10px;">
                        <label>Tiêu đề:</label><br>
                        <input type="text" name="title" required style="width: 100%; padding: 8px;">
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label>Mô tả / Hướng dẫn:</label><br>
                        <textarea name="description" rows="3" style="width: 100%; padding: 8px;"></textarea>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label>Deadline:</label><br>
                        <input type="datetime-local" name="deadline" required style="width: 100%; padding: 8px;">
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label>File đề bài (pdf, docx, txt, zip, rar - Max 100MB):</label><br>
                        <input type="file" name="assignment_file" required accept=".pdf,.doc,.docx,.txt,.zip,.rar">
                    </div>
                    <button type="submit" class="btn" style="background: #28a745;">Giao Bài</button>
                </form>
            </div>
        <?php endif; ?>

        <hr>

        <!-- DANH SÁCH BÀI TẬP -->
        <h3>Danh Sách Bài Tập Của Lớp</h3>
        <?php if (empty($assignments)): ?>
            <p>Hiện chưa có bài tập nào.</p>
        <?php else: ?>
            <?php foreach ($assignments as $hw): ?>
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <h4 style="margin-top: 0; margin-bottom: 5px; color: #333;"><?= htmlspecialchars($hw['title']) ?></h4>
                            <p style="font-size: 13px; color: #666; margin: 0 0 10px 0;">
                                Giáo viên: <strong><?= htmlspecialchars($hw['teacher_name']) ?></strong> |
                                Hạn nộp: <strong style="color: #dc3545;"><?= date('d/m/Y H:i', strtotime($hw['deadline'])) ?></strong>
                            </p>
                        </div>
                        <!-- Nút Xóa bài tập cho chính Giáo viên tạo -->
                        <?php if ($currentUser['role'] === 'teacher' && $currentUser['id'] == $hw['teacher_id']): ?>
                            <form action="/assignments/delete" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa bài tập này và toàn bộ bài nộp của sinh viên?');">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <input type="hidden" name="assignment_id" value="<?= $hw['id'] ?>">
                                <button type="submit" class="btn" style="background: #dc3545; padding: 5px 10px; font-size: 12px;">Xóa Bài</button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <p><?= nl2br(htmlspecialchars($hw['description'])) ?></p>
                    <p><a href="<?= htmlspecialchars($hw['file_path']) ?>" class="btn" style="background: #17a2b8; text-decoration: none;" download>Tải Đề Bài Về</a></p>

                    <!-- SINH VIÊN -->
                    <?php if ($currentUser['role'] === 'student'): ?>
                        <div style="margin-top: 15px; border-top: 1px dashed #ccc; padding-top: 15px;">

                            <!-- Hiển thị bài nộp hiện tại và Điểm -->
                            <?php if (!empty($mySubmissions[$hw['id']])): ?>
                                <?php
                                $mySub = $mySubmissions[$hw['id']];
                                $isLate = strtotime($mySub['created_at']) > strtotime($hw['deadline']);
                                ?>
                                <div style="margin-bottom: 15px; padding: 10px; background: #f4f8fa; border-radius: 4px; border-left: 4px solid #17a2b8;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div>
                                            <strong>Trạng thái:</strong> Đã nộp lúc <?= date('d/m/Y H:i', strtotime($mySub['created_at'])) ?>
                                            <?php if ($isLate): ?>
                                                <span style="background: #dc3545; color: white; padding: 2px 6px; border-radius: 4px; font-size: 12px; margin-left: 5px;">Nộp trễ</span>
                                            <?php else: ?>
                                                <span style="background: #28a745; color: white; padding: 2px 6px; border-radius: 4px; font-size: 12px; margin-left: 5px;">Đúng hạn</span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Nút Gỡ Bài Nộp -->
                                        <form action="/assignments/unsubmit" method="POST" onsubmit="return confirm('Bạn muốn gỡ bài nộp này?');">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                            <input type="hidden" name="submission_id" value="<?= $mySub['id'] ?>">
                                            <button type="submit" class="btn" style="background: #6c757d; padding: 3px 8px; font-size: 12px;">Gỡ bài</button>
                                        </form>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <strong>Điểm số:</strong>
                                        <?php if (isset($mySub['score'])): ?>
                                            <span style="color: #dc3545; font-weight: bold; font-size: 18px;"><?= htmlspecialchars($mySub['score']) ?> / 10</span>
                                        <?php else: ?>
                                            <span style="color: #888; font-style: italic;">Giáo viên chưa chấm điểm</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Form nộp bài -->
                            <strong><?= empty($mySubmissions[$hw['id']]) ? 'Nộp bài làm:' : 'Nộp lại bài:' ?></strong>
                            <form action="/assignments/submit" method="POST" enctype="multipart/form-data" style="margin-top: 10px;">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <input type="hidden" name="assignment_id" value="<?= $hw['id'] ?>">
                                <input type="file" name="submission_file" required accept=".pdf,.doc,.docx,.txt,.zip,.rar">
                                <button type="submit" class="btn"><?= empty($mySubmissions[$hw['id']]) ? 'Nộp Bài' : 'Nộp Lại' ?></button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- GIÁO VIÊN -->
                    <?php if ($currentUser['role'] === 'teacher'): ?>
                        <div class="submission-list">
                            <strong>Danh sách nộp bài (<?= count($submissions[$hw['id']] ?? []) ?>):</strong>
                            <?php if (empty($submissions[$hw['id']])): ?>
                                <p style="font-size: 13px; color: #777;">Chưa có sinh viên nào nộp bài.</p>
                            <?php else: ?>
                                <ul style="margin: 5px 0; padding-left: 20px;">
                                    <?php foreach ($submissions[$hw['id']] as $sub): ?>
                                        <?php $isLate = strtotime($sub['created_at']) > strtotime($hw['deadline']); ?>
                                        <li style="margin-bottom: 10px; display: flex; align-items: center; flex-wrap: wrap;">
                                            <strong style="color: #007bff; margin-right: 5px;"><?= htmlspecialchars($sub['student_name']) ?></strong>
                                            <span style="font-size: 12px; color: #888; margin-right: 5px;">(<?= date('d/m/Y H:i', strtotime($sub['created_at'])) ?>)</span>

                                            <?php if ($isLate): ?>
                                                <span style="color: #dc3545; font-size: 11px; font-weight: bold; margin-right: 10px;">[Trễ]</span>
                                            <?php endif; ?>

                                            <a href="<?= htmlspecialchars($sub['file_path']) ?>" target="_blank" style="margin-right: auto;">Tải bài làm</a>

                                            <!-- Form nhập điểm -->
                                            <form action="/assignments/grade" method="POST" style="display: inline-block;">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                <input type="hidden" name="submission_id" value="<?= $sub['id'] ?>">
                                                <input type="number" step="0.1" min="0" max="10" name="score" value="<?= $sub['score'] ?? '' ?>" placeholder="Điểm" style="width: 60px; padding: 4px;" required>
                                                <button type="submit" class="btn" style="padding: 4px 8px; font-size: 12px; background: #28a745;">Lưu</button>
                                            </form>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div id="toast-container"></div>
    <script src="/assets/js/script.js"></script>
    <script>
        <?php if (!empty($toastError)): ?> showToast("<?= htmlspecialchars($toastError) ?>", "error");
        <?php endif; ?>
        <?php if (!empty($toastSuccess)): ?> showToast("<?= htmlspecialchars($toastSuccess) ?>", "success");
        <?php endif; ?>
    </script>
</body>

</html>