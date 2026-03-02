<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
</head>
<body>
    <h1>This is home page</h1>
    <p>Xin chào, <?php echo htmlspecialchars($user['full_name']); ?>! Bạn đã đăng nhập với vai trò: <?php echo htmlspecialchars($user['role']); ?>.</p>
</body>
</html>