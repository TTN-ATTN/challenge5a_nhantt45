<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - FORBIDDEN</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center text-center p-6">
    <div class="bg-white p-10 rounded-lg shadow-md max-w-md w-full border-t-4 border-red-500">
        <h1 class="text-6xl font-bold text-red-500 mb-2">403</h1>
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">FORBIDDEN</h2>
        <?php if (isset($errorMessage)): ?>
            <p class="text-gray-600 mb-6 font-medium"><?= htmlspecialchars($errorMessage) ?></p>
        <?php else: ?>
            <p class="text-gray-600 mb-6">Bạn không có quyền truy cập vào tài nguyên này.</p>
        <?php endif; ?>
        <a href="/" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-6 rounded shadow transition">&larr; Quay lại trang chủ</a>
    </div>
</body>

</html>

</html>