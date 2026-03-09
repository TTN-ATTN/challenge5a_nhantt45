<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Trò Chơi Giải Đố</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .card {
            background: white;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .success-box {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
            font-family: "Courier New", Courier, monospace;
            white-space: pre-wrap;
            line-height: 1.6;
        }
    </style>
</head>

<body>
    <a href="/">&larr; Quay lại trang chủ</a>

    <div style="max-width: 800px; margin: 20px auto;">
        <h2 style="color: #6f42c1;">Trò Chơi Giải Đố</h2>

        <!-- GIÁO VIÊN: TẠO THỬ THÁCH -->
        <?php if ($currentUser['role'] === 'teacher'): ?>
            <div class="card" style="background: #f8f9fa; border-left: 4px solid #6f42c1;">
                <h3 style="color: #6f42c1;">+ Tạo Challenge Mới</h3>
                <form action="/challenges/create" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                    <div style="margin-bottom: 15px;">
                        <label>Gợi ý (Hint):</label><br>
                        <textarea name="hint" rows="2" style="width: 100%; padding: 8px;" placeholder="Ví dụ: Tên một bài hát của Sơn Tùng M-TP" required></textarea>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="color: #dc3545; font-weight: bold;">Upload File (.txt):</label><br>
                        <p style="font-size: 13px; color: #666; margin-top: 5px;">Tên file chính là đáp án (viết không dấu, cách nhau khoảng trắng, vd: <strong>Em cua ngay hom qua.txt</strong>)</p>
                        <input type="file" name="challenge_file" required accept=".txt">
                    </div>
                    <button type="submit" class="btn" style="background: #6f42c1;">Tạo Challenge</button>
                </form>
            </div>
        <?php endif; ?>

        <hr style="margin: 30px 0;">

        <h3>Danh Sách Thử Thách</h3>
        <?php if (empty($challenges)): ?>
            <p style="font-style: italic; color: #888;">Chưa có thử thách nào.</p>
        <?php else: ?>
            <?php foreach ($challenges as $chall): ?>
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <h4 style="margin-top: 0; margin-bottom: 5px; color: #333;">Challenge #<?= $chall['id'] ?></h4>
                            <p style="font-size: 13px; color: #666; margin: 0 0 10px 0;">Giáo viên tạo: <strong><?= htmlspecialchars($chall['teacher_name']) ?></strong> | Ngày: <?= date('d/m/Y', strtotime($chall['created_at'])) ?></p>
                        </div>

                        <!-- Nếu là chủ sở hữu, hiện nút Sửa / Xóa -->
                        <?php if ($currentUser['role'] === 'teacher' && $currentUser['id'] == $chall['teacher_id']): ?>
                            <div style="display: flex; gap: 10px;">
                                <button onclick="toggleChallEdit(<?= $chall['id'] ?>)" class="btn" style="background: #17a2b8; padding: 4px 8px; font-size: 12px; margin: 0;">Sửa</button>
                                <form action="/challenges/delete" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa thử thách này?');" style="margin: 0;">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                    <input type="hidden" name="challenge_id" value="<?= $chall['id'] ?>">
                                    <button type="submit" class="btn" style="background: #dc3545; padding: 4px 8px; font-size: 12px; margin: 0;">Xóa</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- xem -->
                    <div id="chall-view-<?= $chall['id'] ?>">
                        <div style="background: #fff3cd; color: #856404; padding: 10px; border-left: 4px solid #ffeeba; margin-bottom: 15px;">
                            <strong>Gợi ý:</strong> <?= nl2br(htmlspecialchars($chall['hint'])) ?>
                        </div>
                    </div>

                    <!-- edit -->
                    <?php if ($currentUser['role'] === 'teacher' && $currentUser['id'] == $chall['teacher_id']): ?>
                        <div id="chall-edit-<?= $chall['id'] ?>" style="display: none; background: #f8f9fa; padding: 15px; border-radius: 5px; border: 1px dashed #ccc; margin-bottom: 15px;">
                            <form action="/challenges/edit" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <input type="hidden" name="challenge_id" value="<?= $chall['id'] ?>">

                                <div style="margin-bottom: 10px;">
                                    <label>Cập nhật Gợi ý:</label><br>
                                    <textarea name="hint" rows="2" style="width: 100%; padding: 8px;" required><?= htmlspecialchars($chall['hint']) ?></textarea>
                                </div>

                                <div style="margin-bottom: 10px;">
                                    <label>Đổi file đáp án mới (.txt) - <i>Để trống nếu không muốn đổi</i>:</label><br>
                                    <input type="file" name="challenge_file" accept=".txt">
                                </div>

                                <div style="text-align: right;">
                                    <button type="button" class="btn" style="background: #6c757d; padding: 4px 8px; font-size: 12px;" onclick="toggleChallEdit(<?= $chall['id'] ?>)">Hủy</button>
                                    <button type="submit" class="btn" style="background: #28a745; padding: 4px 8px; font-size: 12px;">Lưu Thay Đổi</button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($solvedId) && $solvedId == $chall['id']): ?>
                        <div class="success-box">
                            <strong style="font-size: 18px;">Chúc mừng! Nội dung:</strong><br><br><?= htmlspecialchars($solvedContent) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($currentUser['role'] === 'student'): ?>
                        <form action="/challenges/solve" method="POST" style="margin-top: 15px; display: flex; gap: 10px;">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                            <input type="hidden" name="challenge_id" value="<?= $chall['id'] ?>">
                            <input type="text" name="answer" placeholder="Nhập đáp án của bạn..." style="flex: 1; padding: 8px;" required>
                            <button type="submit" class="btn" style="background: #28a745; margin: 0;">Giải Đố</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div id="toast-container"></div>
    <script src="/assets/js/script.js"></script>
    <script src="/assets/js/chall.js"></script>
    <script>
        <?php if (!empty($toastError)): ?> showToast("<?= htmlspecialchars($toastError) ?>", "error");
        <?php endif; ?>
        <?php if (!empty($toastSuccess)): ?> showToast("<?= htmlspecialchars($toastSuccess) ?>", "success");
        <?php endif; ?>
    </script>
</body>

</html>