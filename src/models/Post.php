<?php

require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/User.php';

/**
 * Post Model
 * 
 * Handles blog post data and operations
 */
class Post
{
    public int $id;
    public int $user_id;
    public string $title;
    public string $content;
    public string $created_at;
    public ?string $updated_at;
    public int $likes;
    public int $dislikes;
    public int $views;

    /**
     * Constructor
     * 
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = (int)$data['id'];
        $this->user_id = (int)$data['user_id'];
        $this->title = $data['title'];
        $this->content = $data['content'];
        $this->created_at = $data['created_at'];
        $this->updated_at = $data['updated_at'] ?? null;
        $this->likes = (int)$data['likes'];
        $this->dislikes = (int)$data['dislikes'];
        $this->views = (int)$data['views'];
    }

    /**
     * Create new post
     * 
     * @param int $user_id
     * @param string $title
     * @param string $content
     * @return Post|false
     */
    public static function create(int $user_id, string $title, string $content): Post|false
    {
        try {
            $db = Database::getConnection();

            $stmt = $db->prepare(
                'INSERT INTO posts (user_id, title, content, likes, dislikes, views) 
                 VALUES (:user_id, :title, :content, 0, 0, 0)'
            );

            $stmt->execute([
                'user_id' => $user_id,
                'title' => $title,
                'content' => $content
            ]);

            $postId = $db->lastInsertId();
            return self::findById((int)$postId);
        } catch (PDOException $e) {
            error_log('Post creation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Find post by ID
     * 
     * @param int $id
     * @return Post|false
     */
    public static function findById(int $id): Post|false
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare('SELECT * FROM posts WHERE id = :id');
            $stmt->execute(['id' => $id]);

            $data = $stmt->fetch();
            return $data ? new self($data) : false;
        } catch (PDOException $e) {
            error_log('Post find by ID failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Find all posts
     * 
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function findAll(int $limit = 50, int $offset = 0): array
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare(
                'SELECT * FROM posts ORDER BY created_at DESC LIMIT :limit OFFSET :offset'
            );
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $posts = [];
            while ($data = $stmt->fetch()) {
                $posts[] = new self($data);
            }

            return $posts;
        } catch (PDOException $e) {
            error_log('Post find all failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Find posts by user ID
     * 
     * @param int $user_id
     * @return array
     */
    public static function findByUserId(int $user_id): array
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare(
                'SELECT * FROM posts WHERE user_id = :user_id ORDER BY created_at DESC'
            );
            $stmt->execute(['user_id' => $user_id]);

            $posts = [];
            while ($data = $stmt->fetch()) {
                $posts[] = new self($data);
            }

            return $posts;
        } catch (PDOException $e) {
            error_log('Post find by user ID failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Update post
     * 
     * @param string $title
     * @param string $content
     * @return bool
     */
    public function update(string $title, string $content): bool
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare(
                'UPDATE posts SET title = :title, content = :content, updated_at = CURRENT_TIMESTAMP 
                 WHERE id = :id'
            );

            $result = $stmt->execute([
                'title' => $title,
                'content' => $content,
                'id' => $this->id
            ]);

            if ($result) {
                $this->title = $title;
                $this->content = $content;
            }

            return $result;
        } catch (PDOException $e) {
            error_log('Post update failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete post
     * 
     * @return bool
     */
    public function delete(): bool
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare('DELETE FROM posts WHERE id = :id');
            return $stmt->execute(['id' => $this->id]);
        } catch (PDOException $e) {
            error_log('Post deletion failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Increment views counter
     * 
     * @return bool
     */
    public function incrementViews(): bool
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare('UPDATE posts SET views = views + 1 WHERE id = :id');
            $result = $stmt->execute(['id' => $this->id]);

            if ($result) {
                $this->views++;
            }

            return $result;
        } catch (PDOException $e) {
            error_log('Post increment views failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Increment likes counter
     * 
     * @return bool
     */
    public function incrementLikes(): bool
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare('UPDATE posts SET likes = likes + 1 WHERE id = :id');
            $result = $stmt->execute(['id' => $this->id]);

            if ($result) {
                $this->likes++;
            }

            return $result;
        } catch (PDOException $e) {
            error_log('Post increment likes failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Increment dislikes counter
     * 
     * @return bool
     */
    public function incrementDislikes(): bool
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare('UPDATE posts SET dislikes = dislikes + 1 WHERE id = :id');
            $result = $stmt->execute(['id' => $this->id]);

            if ($result) {
                $this->dislikes++;
            }

            return $result;
        } catch (PDOException $e) {
            error_log('Post increment dislikes failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get post author
     * 
     * @return User|false
     */
    public function getAuthor(): User|false
    {
        return User::findById($this->user_id);
    }

    /**
     * Get post comments
     * 
     * @return array
     */
    public function getComments(): array
    {
        require_once __DIR__ . '/Comment.php';
        return Comment::findByPostId($this->id);
    }

    /**
     * Get comments count
     * 
     * @return int
     */
    public function getCommentsCount(): int
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare('SELECT COUNT(*) as count FROM comments WHERE post_id = :post_id');
            $stmt->execute(['post_id' => $this->id]);
            $result = $stmt->fetch();
            return (int)$result['count'];
        } catch (PDOException $e) {
            error_log('Post get comments count failed: ' . $e->getMessage());
            return 0;
        }
    }
}
