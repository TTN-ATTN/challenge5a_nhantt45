<?php
$pageTitle = '403 - Không có quyền truy cập';
$mainClass = 'flex items-center justify-center';
require __DIR__ . '/../layout/header.php';
?>

<div class="bg-white p-10 rounded-lg shadow-md max-w-md w-full border-t-4 border-green-500 text-center">
    <h1 class="text-6xl font-bold text-green-500 mb-2">403</h1>
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Không có quyền truy cập</h2>
    <p class="text-red-600 mb-6"><?= htmlspecialchars($errorMessage) ?></p>
    <a href="/" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-6 rounded shadow transition">
        ← Quay lại trang chủ
    </a>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>