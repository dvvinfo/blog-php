<?php
$pageTitle = 'Аналитика профиля';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Аналитика профиля</h1>
    <p class="text-gray-600 mt-2">Статистика ваших постов и активности</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Всего постов</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?= $totalPosts ?></p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <span class="text-3xl">📝</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Просмотров</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?= $totalViews ?></p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <span class="text-3xl">👁️</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Лайков</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?= $totalLikes ?></p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <span class="text-3xl">👍</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Дизлайков</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?= $totalDislikes ?></p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <span class="text-3xl">👎</span>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Комментариев</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?= $totalComments ?></p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <span class="text-3xl">💬</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Средний рейтинг</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">
                    <?php 
                    $totalReactions = $totalLikes + $totalDislikes;
                    if ($totalReactions > 0) {
                        $rating = round(($totalLikes / $totalReactions) * 100);
                        echo $rating . '%';
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </p>
            </div>
            <div class="bg-indigo-100 rounded-full p-3">
                <span class="text-3xl">⭐</span>
            </div>
        </div>
    </div>
</div>

<!-- User Posts List -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Мои посты</h2>

    <?php if (empty($userPosts)): ?>
        <p class="text-gray-600 text-center py-8">У вас пока нет постов</p>
        <div class="text-center">
            <a href="/posts/create" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                Создать первый пост
            </a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Заголовок</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Просмотры</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Лайки</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Дизлайки</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Комментарии</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($userPosts as $post): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <a href="/posts/<?= $post->id ?>" class="text-blue-500 hover:text-blue-600 font-medium">
                                    <?= htmlspecialchars($post->title) ?>
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?= date('d.m.Y', strtotime($post->created_at)) ?>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600">
                                <?= $post->views ?>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600">
                                <?= $post->likes ?>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600">
                                <?= $post->dislikes ?>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600">
                                <?= $post->getCommentsCount() ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="/posts/<?= $post->id ?>/edit" class="text-yellow-600 hover:text-yellow-700 mr-3">
                                    Редактировать
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
