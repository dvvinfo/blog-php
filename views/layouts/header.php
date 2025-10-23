<?php
require_once __DIR__ . '/../../src/middleware/JWTMiddleware.php';
$isAuthenticated = JWTMiddleware::isAuthenticated();
$userLogin = JWTMiddleware::getUserLogin();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Личный блог' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <nav class="bg-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex space-x-7 items-center">
                    <div>
                        <a href="/" class="flex items-center">
                            <span class="font-semibold text-gray-800 text-lg">Личный блог</span>
                        </a>
                    </div>
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="/" class="py-2 px-3 text-gray-700 hover:text-blue-500 transition duration-300">Главная</a>
                        <?php if ($isAuthenticated): ?>
                            <a href="/profile/analytics" class="py-2 px-3 text-gray-700 hover:text-blue-500 transition duration-300">Мой профиль</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <?php if ($isAuthenticated && $userLogin): ?>
                        <span class="text-gray-700">Привет, <strong><?= htmlspecialchars($userLogin) ?></strong></span>
                        <a href="/logout" class="py-2 px-4 bg-red-500 hover:bg-red-600 text-white rounded transition duration-300">Выход</a>
                    <?php else: ?>
                        <a href="/login" class="py-2 px-4 text-gray-700 hover:text-blue-500 transition duration-300">Вход</a>
                        <a href="/register" class="py-2 px-4 bg-blue-500 hover:bg-blue-600 text-white rounded transition duration-300">Регистрация</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <main class="flex-grow max-w-6xl mx-auto px-4 py-8 w-full">
