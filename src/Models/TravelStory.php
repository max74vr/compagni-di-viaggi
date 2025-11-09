<?php
/**
 * TravelStory Model
 * Compagni di Viaggi - Racconti di Viaggi
 */

class TravelStory {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Create a new travel story
     */
    public function create($data) {
        $sql = "INSERT INTO travel_stories
                (user_id, title, story_content, destination, country, travel_date, travel_duration,
                 travel_type, cover_image, tips, budget_info, highlights, would_return, overall_rating, is_published)
                VALUES
                (:user_id, :title, :story_content, :destination, :country, :travel_date, :travel_duration,
                 :travel_type, :cover_image, :tips, :budget_info, :highlights, :would_return, :overall_rating, :is_published)";

        $stmt = $this->db->prepare($sql);

        $params = [
            ':user_id' => $data['user_id'],
            ':title' => $data['title'],
            ':story_content' => $data['story_content'],
            ':destination' => $data['destination'],
            ':country' => $data['country'],
            ':travel_date' => $data['travel_date'] ?? null,
            ':travel_duration' => $data['travel_duration'] ?? null,
            ':travel_type' => $data['travel_type'] ?? null,
            ':cover_image' => $data['cover_image'] ?? null,
            ':tips' => $data['tips'] ?? null,
            ':budget_info' => $data['budget_info'] ?? null,
            ':highlights' => $data['highlights'] ?? null,
            ':would_return' => $data['would_return'] ?? true,
            ':overall_rating' => $data['overall_rating'] ?? null,
            ':is_published' => $data['is_published'] ?? true
        ];

        if ($stmt->execute($params)) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Get story by ID
     */
    public function getById($id) {
        $sql = "SELECT ts.*, u.username, u.first_name, u.last_name, u.profile_photo, u.reputation_score
                FROM travel_stories ts
                INNER JOIN users u ON ts.user_id = u.id
                WHERE ts.id = :id AND ts.is_published = 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        // Increment views count
        $this->incrementViews($id);

        return $stmt->fetch();
    }

    /**
     * Get all published stories with filters
     */
    public function getAll($filters = [], $page = 1, $limit = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $limit;

        $sql = "SELECT ts.*, u.username, u.first_name, u.last_name, u.profile_photo, u.reputation_score
                FROM travel_stories ts
                INNER JOIN users u ON ts.user_id = u.id
                WHERE ts.is_published = 1";

        $params = [];

        // Apply filters
        if (!empty($filters['destination'])) {
            $sql .= " AND (ts.destination LIKE :destination OR ts.country LIKE :destination)";
            $params[':destination'] = '%' . $filters['destination'] . '%';
        }

        if (!empty($filters['travel_type'])) {
            $sql .= " AND ts.travel_type = :travel_type";
            $params[':travel_type'] = $filters['travel_type'];
        }

        if (!empty($filters['user_id'])) {
            $sql .= " AND ts.user_id = :user_id";
            $params[':user_id'] = $filters['user_id'];
        }

        if (!empty($filters['country'])) {
            $sql .= " AND ts.country = :country";
            $params[':country'] = $filters['country'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (ts.title LIKE :search OR ts.story_content LIKE :search OR ts.tips LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        // Order by most recent
        $sql .= " ORDER BY ts.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        // Bind limit and offset separately as integers
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        // Bind other parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get user's stories
     */
    public function getUserStories($userId) {
        $sql = "SELECT ts.*, u.username, u.first_name, u.last_name, u.profile_photo
                FROM travel_stories ts
                INNER JOIN users u ON ts.user_id = u.id
                WHERE ts.user_id = :user_id
                ORDER BY ts.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Get featured/popular stories
     */
    public function getFeatured($limit = 6) {
        $sql = "SELECT ts.*, u.username, u.first_name, u.last_name, u.profile_photo, u.reputation_score
                FROM travel_stories ts
                INNER JOIN users u ON ts.user_id = u.id
                WHERE ts.is_published = 1
                ORDER BY ts.likes_count DESC, ts.views_count DESC, ts.created_at DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Update story
     */
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];

        $allowedFields = ['title', 'story_content', 'destination', 'country', 'travel_date',
                          'travel_duration', 'travel_type', 'cover_image', 'tips', 'budget_info',
                          'highlights', 'would_return', 'overall_rating', 'is_published'];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE travel_stories SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete story
     */
    public function delete($id) {
        $sql = "DELETE FROM travel_stories WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Increment views count
     */
    private function incrementViews($id) {
        $sql = "UPDATE travel_stories SET views_count = views_count + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    /**
     * Toggle like on story
     */
    public function toggleLike($storyId, $userId) {
        // Check if already liked
        $sql = "SELECT id FROM travel_story_likes WHERE story_id = :story_id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':story_id' => $storyId, ':user_id' => $userId]);

        if ($stmt->fetch()) {
            // Unlike
            $sql = "DELETE FROM travel_story_likes WHERE story_id = :story_id AND user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':story_id' => $storyId, ':user_id' => $userId]);

            // Decrement likes count
            $sql = "UPDATE travel_stories SET likes_count = likes_count - 1 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $storyId]);

            return false; // Unliked
        } else {
            // Like
            $sql = "INSERT INTO travel_story_likes (story_id, user_id) VALUES (:story_id, :user_id)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':story_id' => $storyId, ':user_id' => $userId]);

            // Increment likes count
            $sql = "UPDATE travel_stories SET likes_count = likes_count + 1 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $storyId]);

            return true; // Liked
        }
    }

    /**
     * Check if user has liked a story
     */
    public function hasUserLiked($storyId, $userId) {
        $sql = "SELECT id FROM travel_story_likes WHERE story_id = :story_id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':story_id' => $storyId, ':user_id' => $userId]);
        return $stmt->fetch() !== false;
    }

    /**
     * Add comment to story
     */
    public function addComment($storyId, $userId, $commentText) {
        $sql = "INSERT INTO travel_story_comments (story_id, user_id, comment_text)
                VALUES (:story_id, :user_id, :comment_text)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':story_id' => $storyId,
            ':user_id' => $userId,
            ':comment_text' => $commentText
        ]);
    }

    /**
     * Get story comments
     */
    public function getComments($storyId) {
        $sql = "SELECT c.*, u.username, u.first_name, u.last_name, u.profile_photo
                FROM travel_story_comments c
                INNER JOIN users u ON c.user_id = u.id
                WHERE c.story_id = :story_id
                ORDER BY c.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':story_id' => $storyId]);
        return $stmt->fetchAll();
    }

    /**
     * Get total count
     */
    public function getTotalCount($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM travel_stories WHERE is_published = 1";
        $params = [];

        if (!empty($filters['destination'])) {
            $sql .= " AND (destination LIKE :destination OR country LIKE :destination)";
            $params[':destination'] = '%' . $filters['destination'] . '%';
        }

        if (!empty($filters['travel_type'])) {
            $sql .= " AND travel_type = :travel_type";
            $params[':travel_type'] = $filters['travel_type'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}
