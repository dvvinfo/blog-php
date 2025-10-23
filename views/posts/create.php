<?php
$pageTitle = 'Создать пост';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md p-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Создать пост</h1>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="/posts/create" class="space-y-4">
        <div>
            <label for="title" class="block text-gray-700 font-medium mb-2">Заголовок</label>
            <input 
                type="text" 
                id="title" 
                name="title" 
                value="<?= htmlspecialchars($title ?? '') ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
            >
        </div>

        <div>
            <label for="content" class="block text-gray-700 font-medium mb-2">Содержимое</label>
            <textarea 
                id="content" 
                name="content" 
                rows="12"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
            ><?= htmlspecialchars($content ?? '') ?></textarea>
        </div>

        <div class="flex space-x-4">
            <button 
                type="submit" 
                class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg transition duration-300"
            >
                Сохранить
            </button>
            <a 
                href="/" 
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-6 rounded-lg transition duration-300"
            >
                Отмена
            </a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
