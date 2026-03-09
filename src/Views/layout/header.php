<?php
$pageTitle = $pageTitle ?? 'Hệ thống Quản lý Lớp học';
$bodyClass = $bodyClass ?? 'bg-gray-100 text-gray-800 font-sans bg-gray-50';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <?php if (isset($extraCss)): ?>
        <?= $extraCss ?>
    <?php endif; ?>
</head>
<body class="<?= htmlspecialchars($bodyClass) ?> flex flex-col min-h-screen">

<?php if (isset($currentUser)): ?>
    <nav class="bg-indigo-600 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo / Tên hệ thống -->
                <div class="flex items-center">
                    <a href="/" class="text-xl font-bold text-white tracking-wider hover:text-indigo-200 transition">Class Manager</a>
                    
                    <!-- Menu chính trên Desktop -->
                    <div class="hidden md:block ml-10">
                        <div class="flex items-baseline space-x-4">
                            <?php $current_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>
                            <a href="/" class="px-3 py-2 rounded-md text-sm font-medium <?= ($current_uri == '/' || $current_uri == '/home') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-500 hover:text-white' ?>">Sinh viên</a>
                            <a href="/assignments" class="px-3 py-2 rounded-md text-sm font-medium <?= strpos($current_uri, '/assignments') === 0 ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-500 hover:text-white' ?>">Bài tập</a>
                            <a href="/challenges" class="px-3 py-2 rounded-md text-sm font-medium <?= strpos($current_uri, '/challenges') === 0 ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-500 hover:text-white' ?>">Giải đố</a>
                        </div>
                    </div>
                </div>

                <!-- Thông tin User & Logout -->
                <div class="hidden md:flex items-center gap-4">
                    <div class="text-sm">
                        <span class="text-indigo-200 text-sm">Chào,</span> <strong class="text-white"><?= htmlspecialchars($currentUser['full_name'] ?? '') ?></strong>
                        <span class="text-indigo-300 text-xs ml-1">(<?= htmlspecialchars($currentUser['role'] ?? '') ?>)</span>
                    </div>
                    <a href="/logout" class="text-sm bg-indigo-700 hover:bg-red-600 text-white px-3 py-1.5 rounded transition">Đăng xuất</a>
                </div>

                <!-- Hamburger menu cho mobile -->
                <div class="-mr-2 flex md:hidden">
                    <button type="button" id="mobile-menu-btn" class="inline-flex items-center justify-center p-2 rounded-md text-indigo-200 hover:text-white hover:bg-indigo-500 focus:outline-none transition">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Menu Mobile -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="/" class="block px-3 py-2 rounded-md text-base font-medium <?= ($current_uri == '/' || $current_uri == '/home') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-500 hover:text-white' ?>">Sinh viên</a>
                <a href="/assignments" class="block px-3 py-2 rounded-md text-base font-medium <?= strpos($current_uri, '/assignments') === 0 ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-500 hover:text-white' ?>">Bài tập</a>
                <a href="/challenges" class="block px-3 py-2 rounded-md text-base font-medium <?= strpos($current_uri, '/challenges') === 0 ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-500 hover:text-white' ?>">Giải đố</a>
                <?php if ($currentUser['role'] === 'teacher'): ?>
                    <a href="/create-student" class="block px-3 py-2 rounded-md text-base font-medium text-green-300 hover:bg-indigo-500 hover:text-white">+ Thêm Sinh Viên</a>
                <?php endif; ?>
                <a href="/logout" class="block px-3 py-2 rounded-md text-base font-medium text-red-300 hover:bg-indigo-500 hover:text-white">Đăng xuất</a>
            </div>
            <div class="pt-4 pb-3 border-t border-indigo-700">
                <div class="flex items-center px-5">
                    <div class="text-base font-medium text-white"><?= htmlspecialchars($currentUser['full_name'] ?? '') ?></div>
                    <div class="text-sm font-medium text-indigo-300 ml-3">(<?= htmlspecialchars($currentUser['role'] ?? '') ?>)</div>
                </div>
            </div>
        </div>
    </nav>
    
    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            var menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
<?php endif; ?>

<main class="flex-grow w-full <?= isset($mainClass) ? $mainClass : 'p-4 sm:p-6' ?>">
