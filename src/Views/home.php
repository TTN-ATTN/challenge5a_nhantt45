<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <title>Dashboard</title>
</head>

<body class="bg-gray-100 text-gray-800 font-sans p-4 sm:p-6">
    <div class="max-w-6xl mx-auto bg-white p-4 sm:p-8 rounded shadow">
        <h2 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6 text-indigo-600 border-b pb-2">Hệ thống Quản lý Lớp học</h2>

        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-indigo-50 p-4 rounded-lg border-l-4 border-indigo-500 gap-3">
            <div>
                Xin chào, <strong class="text-indigo-700"><?= htmlspecialchars($currentUser['full_name'] ?? '') ?></strong>
                <span class="text-gray-600 text-sm block sm:inline">(Vai trò: <?= htmlspecialchars($currentUser['role'] ?? '') ?>)</span>
            </div>
            <a href="/logout" class="text-red-500 hover:text-red-700 font-semibold transition">Đăng xuất</a>
        </div>
        
        <div class="mb-6 flex flex-col sm:flex-row flex-wrap gap-3">
            <a href="/assignments" class="text-center sm:text-left inline-block px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded shadow transition flex-1 sm:flex-none">Bài Tập</a>
            <a href="/challenges" class="text-center sm:text-left inline-block px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded shadow transition flex-1 sm:flex-none">Trò chơi giải đố</a>

            <?php if ($currentUser['role'] === 'teacher'): ?>
                <a href="/create-student" class="text-center sm:text-left inline-block px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded shadow transition w-full sm:w-auto sm:ml-auto">+ Thêm sinh viên mới</a>
            <?php endif; ?>
        </div>

        <h3 class="text-2xl font-semibold mb-4 text-gray-700">Danh sách Người dùng</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse bg-white shadow-sm rounded-lg overflow-hidden">
                <thead class="bg-gray-200 text-gray-700 uppercase text-sm font-semibold">
                    <tr>
                        <th class="py-3 px-4 border-b">ID</th>
                        <th class="py-3 px-4 border-b">Tên đăng nhập</th>
                        <th class="py-3 px-4 border-b">Họ tên</th>
                        <th class="py-3 px-4 border-b">Email</th>
                        <th class="py-3 px-4 border-b">Vai trò</th>
                        <th class="py-3 px-4 border-b text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600">
                    <?php foreach ($allUsers as $u): ?>
                        <tr class="hover:bg-gray-50 border-b last:border-0 transition">
                            <td class="py-3 px-4"><?= htmlspecialchars($u['id']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($u['username']) ?></td>
                            <td class="py-3 px-4 font-medium text-gray-800"><?= htmlspecialchars($u['full_name']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($u['email']) ?></td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 text-xs rounded-full <?= $u['role'] === 'teacher' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' ?>">
                                    <?= $u['role'] === 'teacher' ? 'Giáo viên' : 'Sinh viên' ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="/profile?id=<?= $u['id'] ?>" class="text-indigo-500 hover:text-indigo-700 font-medium hover:underline">Xem chi tiết</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 flex flex-col gap-2"></div>
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