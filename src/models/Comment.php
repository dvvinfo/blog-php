<?php

require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/User.php';

/**
 * Comment Model
 * 
 * Handles comment data and operations
 */
class Comment
{
    public int $id;
    public int $post_id;
    public int $user_id;
    public string $text;
    public string $created_at;

    /**
     * Constructor
     * 
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = (int)$data['id'];
        $this->post_id = (int)$data['post_id'];
        $this->user_id = (int)$data['user_id'];
        $this->text = $data['text'];
        $this->created_at = $data['created_at'];
    }

    /**
     * Create new comment
     * 
     * @param int $post_id
     * @param int $user_id
     * @param string $text
     * @return Comment|false
     */
    public static function create(int $post_id, int $user_id, string $text): Comment|false
    {
        try {
            $db = Database::getConnection();

            $stmt = $db->prepare(
                'INSERT INTO comments (post_id, user_id, text) VALUES (:post_id, :user_id, :text)'
            );

            $stmt->execute([
                'post_id' => $post_id,
                'user_id' => $user_id,
                'text' => $text
            ]);

            $commentId = $db->lastInsertId();
            return self::findById((int)$commentId);
        } catch (PDOException $e) {
            error_log('Comment creation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Find comment by ID
     * 
     * @param int $id
     * @return Comment|false
     */
    public static function findById(int $id): Comment|false
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare('SELECT * FROM comments WHERE id = :id');
            $stmt->execute(['id' => $id]);

            $data = $stmt->fetch();
            return $data ? new self($data) : false;
        } catch (PDOException $e) {
            error_log('Comment find by ID failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Find comments by post ID
     * 
     * @param int $post_id
     * @return array
     */
    public static function findByPostId(int $post_id): array
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare(
                'SELECT * FROM comments WHERE post_id = :post_id ORDER BY created_at ASC'
            );
            $stmt->execute(['post_id' => $post_id]);

            $comments = [];
            while ($data = $stmt->fetch()) {
                $comments[] = new self($data);
            }

            return $comments;
        } catch (PDOException $e) {
            error_log('Comment find by post ID failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get comment author
     * 
     * @return User|false
     */
    public function getAuthor(): User|false
    {
        return User::findById($this->user_id);
    }
}
