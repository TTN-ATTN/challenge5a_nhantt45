<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sinh Viên Mới</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="bg-gray-100 font-sans p-4 sm:p-6 text-gray-800">
    <div class="max-w-4xl mx-auto">
        <a href="/" class="text-indigo-600 hover:text-indigo-800 font-medium inline-block mb-4 transition">&larr; Quay lại trang chủ</a>

        <div class="bg-white p-8 rounded-lg shadow-sm">
            <h2 class="text-2xl font-bold text-green-600 mb-4 border-b pb-2">Thêm Sinh Viên Mới</h2>
            
            <form id="createStudentForm" action="/create-student" method="POST" class="mt-4 space-y-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="current_password" id="create_current_password" value="">

                <div>
                    <label class="block text-gray-700 font-medium mb-1">Tên đăng nhập (*):</label>
                    <input type="text" name="username" required 
                           class="w-full max-w-md px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Họ và tên (*):</label>
                    <input type="text" name="full_name" required 
                           class="w-full max-w-md px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Email (*):</label>
                    <input type="email" name="email" required 
                           class="w-full max-w-md px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Số điện thoại (*):</label>
                    <input type="text" name="phone" required 
                           class="w-full max-w-md px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Mật khẩu khởi tạo cho SV (*):</label>
                    <input type="password" name="password" required 
                           class="w-full max-w-md px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                </div>

                <button type="button" class="mt-6 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-md shadow transition" onclick="openModal('create', null)">+ Tạo sinh viên</button>
            </form>
        </div>
    </div>

    <div id="toast-container" class="fixed bottom-4 right-4 z-50 flex flex-col gap-2"></div>

    <div id="passwordModal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex justify-center items-center backdrop-blur-sm transition-opacity">
        <div class="bg-white p-6 rounded-lg w-full max-w-sm text-center shadow-xl">
            <h3 id="modalTitle" class="text-xl font-bold text-green-600 mb-2">Xác nhận</h3>
            <p id="modalDesc" class="text-sm text-gray-600 mb-6">Nhập mật khẩu Giáo viên để xác nhận tạo sinh viên.</p>

            <input type="password" id="modal_password" 
                   class="w-full px-4 py-2 mb-6 border rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition" 
                   placeholder="Mật khẩu hiện tại...">

            <div class="flex justify-between gap-4">
                <button type="button" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 rounded-md transition" onclick="closeModal()">Hủy</button>
                <button type="button" id="modalConfirmBtn" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-md transition" onclick="submitAction()">Xác nhận</button>
            </div>
        </div>
    </div>

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