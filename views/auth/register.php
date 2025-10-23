<?php
$pageTitle = 'Регистрация';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Регистрация</h1>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="/register" class="space-y-4">
        <div>
            <label for="login" class="block text-gray-700 font-medium mb-2">Логин</label>
            <input 
                type="text" 
                id="login" 
                name="login" 
                value="<?= htmlspecialchars($login ?? '') ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
            >
        </div>

        <div>
            <label for="password" class="block text-gray-700 font-medium mb-2">Пароль</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
            >
        </div>

        <div>
            <label for="confirm_password" class="block text-gray-700 font-medium mb-2">Подтвердите пароль</label>
            <input 
                type="password" 
                id="confirm_password" 
                name="confirm_password" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
            >
        </div>

        <button 
            type="submit" 
            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300"
        >
            Зарегистрироваться
        </button>
    </form>

    <p class="text-center text-gray-600 mt-4">
        Уже есть аккаунт? <a href="/login" class="text-blue-500 hover:text-blue-600">Войти</a>
    </p>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
