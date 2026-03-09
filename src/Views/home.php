<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <title>Dashboard</title>
</head>

<body>
    <h2>Hệ thống Quản lý Lớp học</h2>

    <div style="margin-bottom: 20px;">
        Xin chào, <strong><?= htmlspecialchars($currentUser['full_name'] ?? '') ?></strong>
        (Vai trò: <?= htmlspecialchars($currentUser['role'] ?? '') ?>) |
        <a href="/logout" class="action-link">Đăng xuất</a>
    </div>
    <a href="/assignments" class="btn" style="display: inline-block; margin-bottom: 20px; background: #17a2b8;">Bài Tập</a>

    <?php if ($currentUser['role'] === 'teacher'): ?>
        <a href="/create-student" class="btn" style="display: inline-block; margin-bottom: 15px; background: #28a745;">+ Thêm sinh viên mới</a>
    <?php endif; ?>

    <h3>Danh sách Người dùng</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên đăng nhập</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Vai trò</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allUsers as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['id']) ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['full_name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= $u['role'] === 'teacher' ? 'Giáo viên' : 'Sinh viên' ?></td>
                    <td>
                        <a href="/profile?id=<?= $u['id'] ?>" class="action-link">Xem chi tiết</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div id="toast-container"></div>
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