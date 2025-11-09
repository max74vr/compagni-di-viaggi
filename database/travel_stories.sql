-- Travel Stories Table
-- Sistema di racconti di viaggi per condividere esperienze e consigli

CREATE TABLE IF NOT EXISTS travel_stories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    story_content TEXT NOT NULL,
    destination VARCHAR(255) NOT NULL,
    country VARCHAR(100) NOT NULL,
    travel_date DATE,
    travel_duration VARCHAR(50), -- es: "1 settimana", "2 mesi"
    travel_type VARCHAR(50), -- 'avventura', 'mare', 'città', 'natura', etc.
    cover_image VARCHAR(255),
    tips TEXT, -- Consigli per chi vuole visitare la destinazione
    budget_info TEXT, -- Informazioni sul budget speso
    highlights TEXT, -- Punti salienti del viaggio
    would_return BOOLEAN DEFAULT TRUE, -- Ci torneresti?
    overall_rating TINYINT CHECK (overall_rating BETWEEN 1 AND 5), -- Valutazione generale del viaggio
    is_published BOOLEAN DEFAULT TRUE,
    views_count INT DEFAULT 0,
    likes_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_destination (destination),
    INDEX idx_country (country),
    INDEX idx_travel_type (travel_type),
    INDEX idx_published (is_published),
    INDEX idx_created_at (created_at),
    FULLTEXT idx_content (title, story_content, tips)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Travel Story Likes Table (opzionale - per gestire i "mi piace")
CREATE TABLE IF NOT EXISTS travel_story_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    story_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (story_id) REFERENCES travel_stories(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_like (story_id, user_id),
    INDEX idx_story (story_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Travel Story Comments Table (opzionale - per i commenti)
CREATE TABLE IF NOT EXISTS travel_story_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    story_id INT NOT NULL,
    user_id INT NOT NULL,
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (story_id) REFERENCES travel_stories(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_story (story_id),
    INDEX idx_user (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
