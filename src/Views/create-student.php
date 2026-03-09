<?php
$pageTitle = 'Thêm sinh viên mới';
require __DIR__ . '/layout/header.php';
?>
<div class="max-w-3xl mx-auto mt-6">
    <div class="bg-white p-6 sm:p-10 rounded-xl shadow-md border border-gray-100">
        
        <div class="flex items-center gap-3 mb-8 border-b border-gray-200 pb-4">
            <div class="bg-green-100 p-2 rounded-lg text-green-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Thêm Sinh Viên Mới</h2>
        </div>
        
        <form id="createStudentForm" action="/create-student" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="current_password" id="create_current_password" value="">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Group 1: Tên đăng nhập -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tên đăng nhập <span class="text-red-500">*</span></label>
                    <input type="text" name="username" required 
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition-all duration-200"
                           placeholder="Nhập username...">
                </div>

                <!-- Group 2: Mật khẩu khởi tạo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu khởi tạo <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition-all duration-200"
                           placeholder="Nhập mật khẩu cho SV...">
                </div>

                <!-- Group 3: Họ và tên -->
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Họ và tên <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" required 
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition-all duration-200"
                           placeholder="Nhập họ và tên đầy đủ...">
                </div>

                <!-- Group 4: Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required 
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition-all duration-200"
                           placeholder="sinhvien@example.com">
                </div>

                <!-- Group 5: Số điện thoại -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" required 
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition-all duration-200"
                           placeholder="Nhập số điện thoại...">
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col sm:flex-row justify-end gap-4">
                <a href="/" class="text-center px-6 py-2.5 text-gray-600 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition-colors cursor-pointer">
                    Hủy bỏ
                </a>
                <button type="button" class="text-center bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-8 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex justify-center items-center gap-2 cursor-pointer" onclick="if(document.getElementById('createStudentForm').checkValidity()) { openModal('create', null); } else { document.getElementById('createStudentForm').reportValidity(); }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tạo sinh viên
                </button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>
