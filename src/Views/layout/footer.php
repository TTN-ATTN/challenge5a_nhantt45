</main>
    
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 flex flex-col gap-2"></div>

    <div id="passwordModal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex justify-center items-center backdrop-blur-sm transition-opacity">
        <div class="bg-white p-6 rounded-lg w-full max-w-sm text-center shadow-xl">
            <h3 id="modalTitle" class="text-xl font-bold text-green-600 mb-2">Xác nhận</h3>
            <p id="modalDesc" class="text-sm text-gray-600 mb-6">Nhập mật khẩu Giáo viên để xác nhận.</p>

            <input type="password" id="modal_password" 
                   class="w-full px-4 py-2 mb-6 border rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                   placeholder="Mật khẩu hiện tại...">

            <div class="flex justify-between gap-4">
                <button type="button" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 rounded-md transition cursor-pointer" onclick="closeModal()">Hủy</button>
                <button type="button" id="modalConfirmBtn" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-md transition cursor-pointer" onclick="submitAction()">Xác nhận</button>
            </div>
        </div>
    </div>

    <script src="/assets/js/script.js"></script>
    
    <?php if (isset($extraScripts)): ?>
        <?php foreach ($extraScripts as $script): ?>
            <script src="<?= htmlspecialchars($script) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

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
