<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <title>Đăng nhập</title>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center font-sans">
    <div class="bg-white p-6 sm:p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-indigo-600">Đăng nhập</h2>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="/login" class="space-y-4">
            <div>
                <label for="username" class="block text-gray-700 font-medium mb-1">Tên đăng nhập:</label>
                <input type="text" id="username" name="username" required 
                       class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            </div>

            <div>
                <label for="password" class="block text-gray-700 font-medium mb-1">Mật khẩu:</label>
                <input type="password" id="password" name="password" required 
                       class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            </div>

            <button type="submit" 
                    class="w-full bg-indigo-600 text-white font-bold py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition">
                Đăng nhập
            </button>    
        </form>
    </div>
</body>
</html>