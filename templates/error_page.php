<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | Enigma</title>
    <script src="/assets/vendors/tailwindcss/script.js"></script>
    <link rel="stylesheet" href="/assets/vendors/font-awesome/css/all.min.css">
    <style>
        .error-container {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }
        .error-code {
            text-shadow: 4px 4px 0px rgba(0,0,0,0.1);
        }
        .home-link {
            transition: all 0.3s ease;
        }
        .home-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="min-h-screen error-container">
    <div class="container mx-auto px-4 flex flex-col items-center justify-center min-h-screen py-12">
        <div class="text-center max-w-2xl">
            <h1 class="text-9xl font-bold text-indigo-600 error-code mb-4"><?= $errorCode ?></h1>
            <h2 class="text-3xl font-bold text-gray-800 mb-4"><?= htmlspecialchars($title) ?></h2>
            <p class="text-lg text-gray-600 mb-8"><?= htmlspecialchars($description) ?></p>
            
            <div class="flex flex-wrap justify-center gap-4">
                <a href="/" class="home-link bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg">
                    <i class="fas fa-home mr-2"></i> На главную
                </a>
                <a href="javascript:history.back()" class="home-link bg-white hover:bg-gray-50 text-gray-800 font-medium py-3 px-6 rounded-lg border border-gray-200">
                    <i class="fas fa-arrow-left mr-2"></i> Вернуться назад
                </a>
            </div>
            
            <?php if (!empty($errorMessage)): ?>
                <div class="mt-8 p-4 bg-gray-50 rounded-lg text-left">
                    <p class="text-sm font-medium text-gray-500">Дополнительная информация:</p>
                    <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($errorMessage) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>