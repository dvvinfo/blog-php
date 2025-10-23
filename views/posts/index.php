<?php
$pageTitle = 'Все посты';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-gray-800">Все посты</h1>
    <?php if ($isAuthenticated): ?>
        <a href="/posts/create" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300">
            Создать пост
        </a>
    <?php endif; ?>
</div>

<?php if (empty($posts)): ?>
    <div class="bg-white rounded-lg shadow-md p-8 text-center">
        <p class="text-gray-600 text-lg">Пока нет постов</p>
        <?php if ($isAuthenticated): ?>
            <a href="/posts/create" class="inline-block mt-4 text-blue-500 hover:text-blue-600">
                Создать первый пост
            </a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="grid gap-6">
        <?php foreach ($posts as $post): ?>
            <?php
            $author = $post->getAuthor();
            $commentsCount = $post->getCommentsCount();
            $preview = mb_substr(strip_tags($post->content), 0, 200);
            if (mb_strlen($post->content) > 200) {
                $preview .= '...';
            }
            ?>
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-300">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">
                    <a href="/posts/<?= $post->id ?>" class="hover:text-blue-500">
                        <?= htmlspecialchars($post->title) ?>
                    </a>
                </h2>
                
                <div class="text-sm text-gray-600 mb-4">
                    <span>Автор: <strong><?= htmlspecialchars($author->login) ?></strong></span>
                    <span class="mx-2">•</span>
                    <span><?= date('d.m.Y H:i', strtotime($post->created_at)) ?></span>
                </div>

                <p class="text-gray-700 mb-4"><?= htmlspecialchars($preview) ?></p>

                <div class="flex items-center justify-between text-sm text-gray-600">
                    <div class="flex space-x-4">
                        <span>👁️ <?= $post->views ?></span>
                        <span>👍 <?= $post->likes ?></span>
                        <span>👎 <?= $post->dislikes ?></span>
                        <span>💬 <?= $commentsCount ?></span>
                    </div>
                    <a href="/posts/<?= $post->id ?>" class="text-blue-500 hover:text-blue-600 font-medium">
                        Читать далее →
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
