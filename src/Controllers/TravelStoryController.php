<?php
/**
 * Travel Story Controller
 * Compagni di Viaggi - Racconti di Viaggi
 */

require_once BASE_PATH . '/src/Models/TravelStory.php';
require_once BASE_PATH . '/src/Models/User.php';

class TravelStoryController {
    private $storyModel;
    private $userModel;

    public function __construct() {
        $this->storyModel = new TravelStory();
        $this->userModel = new User();
    }

    /**
     * Show all stories (public page)
     */
    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        $filters = [
            'destination' => $_GET['destination'] ?? '',
            'travel_type' => $_GET['travel_type'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        $stories = $this->storyModel->getAll($filters, $page);
        $totalStories = $this->storyModel->getTotalCount($filters);
        $totalPages = ceil($totalStories / ITEMS_PER_PAGE);

        require_once BASE_PATH . '/src/Views/stories/index.php';
    }

    /**
     * Show single story
     */
    public function show($id) {
        $story = $this->storyModel->getById($id);

        if (!$story) {
            setFlashMessage('Racconto non trovato', 'error');
            redirect('/stories.php');
            return;
        }

        $comments = $this->storyModel->getComments($id);
        $userHasLiked = false;

        if (isLoggedIn()) {
            $userHasLiked = $this->storyModel->hasUserLiked($id, getCurrentUserId());
        }

        require_once BASE_PATH . '/src/Views/stories/show.php';
    }

    /**
     * Show create story form
     */
    public function showCreateForm() {
        if (!isLoggedIn()) {
            setFlashMessage('Devi essere autenticato per creare un racconto', 'error');
            redirect('/login.php');
            return;
        }

        require_once BASE_PATH . '/src/Views/stories/create.php';
    }

    /**
     * Handle story creation
     */
    public function create() {
        if (!isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/login.php');
            return;
        }

        $errors = [];

        // Validate input
        $title = sanitize($_POST['title'] ?? '');
        $storyContent = sanitize($_POST['story_content'] ?? '');
        $destination = sanitize($_POST['destination'] ?? '');
        $country = sanitize($_POST['country'] ?? '');

        if (empty($title) || strlen($title) < 10) {
            $errors[] = 'Il titolo deve essere almeno 10 caratteri';
        }

        if (empty($storyContent) || strlen($storyContent) < 100) {
            $errors[] = 'Il racconto deve essere almeno 100 caratteri';
        }

        if (empty($destination)) {
            $errors[] = 'La destinazione è obbligatoria';
        }

        if (empty($country)) {
            $errors[] = 'Il paese è obbligatorio';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            redirect('/create-story.php');
            return;
        }

        $storyData = [
            'user_id' => getCurrentUserId(),
            'title' => $title,
            'story_content' => $storyContent,
            'destination' => $destination,
            'country' => $country,
            'travel_date' => $_POST['travel_date'] ?? null,
            'travel_duration' => sanitize($_POST['travel_duration'] ?? ''),
            'travel_type' => $_POST['travel_type'] ?? null,
            'tips' => sanitize($_POST['tips'] ?? ''),
            'budget_info' => sanitize($_POST['budget_info'] ?? ''),
            'highlights' => sanitize($_POST['highlights'] ?? ''),
            'would_return' => isset($_POST['would_return']) ? 1 : 0,
            'overall_rating' => !empty($_POST['overall_rating']) ? (int)$_POST['overall_rating'] : null,
            'is_published' => isset($_POST['is_published']) ? 1 : 0
        ];

        // Handle cover image upload
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            // Create stories directory if doesn't exist
            $storiesDir = UPLOAD_DIR . 'stories/';
            if (!is_dir($storiesDir)) {
                mkdir($storiesDir, 0755, true);
            }

            $uploadResult = uploadFile($_FILES['cover_image'], $storiesDir);
            if ($uploadResult['success']) {
                $storyData['cover_image'] = $uploadResult['filename'];
            }
        }

        $storyId = $this->storyModel->create($storyData);

        if ($storyId) {
            setFlashMessage('Racconto pubblicato con successo! 🎉', 'success');
            redirect('/story.php?id=' . $storyId);
        } else {
            setFlashMessage('Errore durante la pubblicazione del racconto', 'error');
            redirect('/create-story.php');
        }
    }

    /**
     * Show edit story form
     */
    public function showEditForm($id) {
        if (!isLoggedIn()) {
            redirect('/login.php');
            return;
        }

        $story = $this->storyModel->getById($id);

        if (!$story) {
            setFlashMessage('Racconto non trovato', 'error');
            redirect('/my-stories.php');
            return;
        }

        // Check if user owns this story
        if ($story['user_id'] != getCurrentUserId()) {
            setFlashMessage('Non hai i permessi per modificare questo racconto', 'error');
            redirect('/stories.php');
            return;
        }

        require_once BASE_PATH . '/src/Views/stories/edit.php';
    }

    /**
     * Handle story update
     */
    public function update($id) {
        if (!isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/login.php');
            return;
        }

        $story = $this->storyModel->getById($id);

        if (!$story || $story['user_id'] != getCurrentUserId()) {
            setFlashMessage('Non autorizzato', 'error');
            redirect('/stories.php');
            return;
        }

        $updateData = [
            'title' => sanitize($_POST['title'] ?? ''),
            'story_content' => sanitize($_POST['story_content'] ?? ''),
            'destination' => sanitize($_POST['destination'] ?? ''),
            'country' => sanitize($_POST['country'] ?? ''),
            'travel_date' => $_POST['travel_date'] ?? null,
            'travel_duration' => sanitize($_POST['travel_duration'] ?? ''),
            'travel_type' => $_POST['travel_type'] ?? null,
            'tips' => sanitize($_POST['tips'] ?? ''),
            'budget_info' => sanitize($_POST['budget_info'] ?? ''),
            'highlights' => sanitize($_POST['highlights'] ?? ''),
            'would_return' => isset($_POST['would_return']) ? 1 : 0,
            'overall_rating' => !empty($_POST['overall_rating']) ? (int)$_POST['overall_rating'] : null,
            'is_published' => isset($_POST['is_published']) ? 1 : 0
        ];

        // Handle cover image upload
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $storiesDir = UPLOAD_DIR . 'stories/';
            $uploadResult = uploadFile($_FILES['cover_image'], $storiesDir);
            if ($uploadResult['success']) {
                $updateData['cover_image'] = $uploadResult['filename'];
            }
        }

        if ($this->storyModel->update($id, $updateData)) {
            setFlashMessage('Racconto aggiornato con successo!', 'success');
        } else {
            setFlashMessage('Errore durante l\'aggiornamento', 'error');
        }

        redirect('/story.php?id=' . $id);
    }

    /**
     * Delete story
     */
    public function delete($id) {
        if (!isLoggedIn()) {
            redirect('/login.php');
            return;
        }

        $story = $this->storyModel->getById($id);

        if (!$story || $story['user_id'] != getCurrentUserId()) {
            setFlashMessage('Non autorizzato', 'error');
            redirect('/stories.php');
            return;
        }

        if ($this->storyModel->delete($id)) {
            setFlashMessage('Racconto eliminato con successo', 'success');
        } else {
            setFlashMessage('Errore durante l\'eliminazione', 'error');
        }

        redirect('/my-stories.php');
    }

    /**
     * Show user's stories
     */
    public function myStories() {
        if (!isLoggedIn()) {
            redirect('/login.php');
            return;
        }

        $userId = getCurrentUserId();
        $stories = $this->storyModel->getUserStories($userId);

        require_once BASE_PATH . '/src/Views/stories/my-stories.php';
    }

    /**
     * Toggle like on story (AJAX)
     */
    public function toggleLike() {
        header('Content-Type: application/json');

        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        $storyId = $_POST['story_id'] ?? null;

        if (!$storyId) {
            echo json_encode(['success' => false, 'error' => 'Story ID required']);
            return;
        }

        $liked = $this->storyModel->toggleLike($storyId, getCurrentUserId());

        echo json_encode(['success' => true, 'liked' => $liked]);
    }

    /**
     * Add comment (AJAX)
     */
    public function addComment() {
        header('Content-Type: application/json');

        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        $storyId = $_POST['story_id'] ?? null;
        $commentText = sanitize($_POST['comment_text'] ?? '');

        if (!$storyId || empty($commentText)) {
            echo json_encode(['success' => false, 'error' => 'Invalid data']);
            return;
        }

        if ($this->storyModel->addComment($storyId, getCurrentUserId(), $commentText)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to add comment']);
        }
    }
}
