<?php
$pageTitle = '404 - Không tìm thấy trang';
$bodyClass = 'bg-gray-100 font-sans min-h-screen flex items-center justify-center text-center p-6';
require __DIR__ . '/../layout/header.php';
?>
    <div class="bg-white p-10 rounded-lg shadow-md max-w-md w-full border-t-4 border-yellow-500">
        <h1 class="text-6xl font-bold text-yellow-500 mb-2">404</h1>
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Không tìm thấy tài nguyên</h2>
        <p class="text-gray-600 mb-6">Đường dẫn hoặc trang bạn yêu cầu không tồn tại trên hệ thống.</p>
        <a href="/" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-6 rounded shadow transition">&larr; Quay lại trang chủ</a>
    </div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
