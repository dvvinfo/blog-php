<?php
$pageTitle = htmlspecialchars($post->title);
require_once __DIR__ . '/../layouts/header.php';
$currentUserId = Session::getUserId();
$isOwner = $currentUserId === $post->user_id;
?>

<div class="bg-white rounded-lg shadow-md p-8 mb-6">
    <div class="flex justify-between items-start mb-4">
        <h1 class="text-3xl font-bold text-gray-800"><?= htmlspecialchars($post->title) ?></h1>
        <?php if ($isOwner): ?>
            <div class="flex space-x-2">
                <a href="/posts/<?= $post->id ?>/edit" class="bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-3 rounded transition duration-300">
                    Редактировать
                </a>
                <form method="POST" action="/posts/<?= $post->id ?>/delete" onsubmit="return confirm('Вы уверены, что хотите удалить этот пост?');" class="inline">
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded transition duration-300">
                        Удалить
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <div class="text-sm text-gray-600 mb-4">
        <span>Автор: <strong><?= htmlspecialchars($author->login) ?></strong></span>
        <span class="mx-2">•</span>
        <span>Создано: <?= date('d.m.Y H:i', strtotime($post->created_at)) ?></span>
        <?php if ($post->updated_at): ?>
            <span class="mx-2">•</span>
            <span>Обновлено: <?= date('d.m.Y H:i', strtotime($post->updated_at)) ?></span>
        <?php endif; ?>
    </div>

    <div class="prose max-w-none mb-6">
        <p class="text-gray-700 whitespace-pre-wrap"><?= htmlspecialchars($post->content) ?></p>
    </div>

    <div class="border-t pt-4">
        <div class="flex items-center justify-between">
            <div class="flex space-x-4 text-gray-600">
                <span>👁️ <?= $post->views ?> просмотров</span>
                <span>💬 <?= count($comments) ?> комментариев</span>
            </div>
            <div class="flex space-x-2">
                <?php if ($isAuthenticated): ?>
                    <form method="POST" action="/posts/<?= $post->id ?>/like" class="inline">
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded transition duration-300">
                            👍 <?= $post->likes ?>
                        </button>
                    </form>
                    <form method="POST" action="/posts/<?= $post->id ?>/dislike" class="inline">
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded transition duration-300">
                            👎 <?= $post->dislikes ?>
                        </button>
                    </form>
                <?php else: ?>
                    <span class="bg-gray-300 text-gray-700 py-2 px-4 rounded cursor-not-allowed">👍 <?= $post->likes ?></span>
                    <span class="bg-gray-300 text-gray-700 py-2 px-4 rounded cursor-not-allowed">👎 <?= $post->dislikes ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Comments Section -->
<div class="bg-white rounded-lg shadow-md p-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Комментарии (<?= count($comments) ?>)</h2>

    <?php if ($isAuthenticated): ?>
        <?php 
        $commentErrors = Session::get('comment_errors');
        if ($commentErrors) {
            Session::set('comment_errors', null);
        }
        ?>
        <?php if (!empty($commentErrors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    <?php foreach ($commentErrors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="/posts/<?= $post->id ?>/comments" class="mb-6">
            <textarea 
                name="text" 
                rows="3" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 mb-2"
                placeholder="Напишите комментарий..."
                required
            ></textarea>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded transition duration-300">
                Отправить
            </button>
        </form>
    <?php else: ?>
        <p class="text-gray-600 mb-6">
            <a href="/login" class="text-blue-500 hover:text-blue-600">Войдите</a>, чтобы оставить комментарий
        </p>
    <?php endif; ?>

    <?php if (empty($comments)): ?>
        <p class="text-gray-600">Пока нет комментариев</p>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($comments as $comment): ?>
                <?php $commentAuthor = $comment->getAuthor(); ?>
                <div class="border-l-4 border-blue-500 pl-4 py-2">
                    <div class="text-sm text-gray-600 mb-1">
                        <strong><?= htmlspecialchars($commentAuthor->login) ?></strong>
                        <span class="mx-2">•</span>
                        <span><?= date('d.m.Y H:i', strtotime($comment->created_at)) ?></span>
                    </div>
                    <p class="text-gray-700"><?= htmlspecialchars($comment->text) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
