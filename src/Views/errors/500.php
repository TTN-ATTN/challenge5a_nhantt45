<?php
$pageTitle = '500 - Lỗi máy chủ';
$mainClass = 'flex items-center justify-center p-6';
require __DIR__ . '/../layout/header.php';
?>

<div class="bg-white p-10 rounded-lg shadow-md max-w-md w-full border-t-4 border-red-500 text-center">
    <h1 class="text-6xl font-bold text-red-500 mb-2">500</h1>
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Lỗi máy chủ</h2>

    <p class="text-red-600 mb-4"><?= htmlspecialchars($errorMessage) ?></p>

    <a href="/" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-6 rounded">
        ← Quay lại trang chủ
    </a>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>