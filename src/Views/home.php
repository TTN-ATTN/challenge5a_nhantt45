<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .action-link { color: #007bff; text-decoration: none; }
        .action-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h2>Hệ thống Quản lý Lớp học</h2>
    
    <div style="margin-bottom: 20px;">
        Xin chào, <strong><?= htmlspecialchars($currentUser['full_name'] ?? '') ?></strong> 
        (Vai trò: <?= htmlspecialchars($currentUser['role'] ?? '') ?>) | 
        <a href="/logout" class="action-link">Đăng xuất</a>
    </div>

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
</body>
</html>