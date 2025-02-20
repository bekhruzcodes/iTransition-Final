-- Users
CREATE TABLE users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       email VARCHAR(180) NOT NULL UNIQUE,
                       roles JSON NOT NULL,
                       password VARCHAR(255) NOT NULL,
                       is_blocked BOOLEAN DEFAULT false,
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                       updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Topics (predefined list)
CREATE TABLE topics (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(50) NOT NULL UNIQUE
);

-- Tags
CREATE TABLE tags (
                      id INT AUTO_INCREMENT PRIMARY KEY,
                      name VARCHAR(50) NOT NULL UNIQUE
);

-- Templates
CREATE TABLE templates (
                           id INT AUTO_INCREMENT PRIMARY KEY,
                           user_id INT NOT NULL,
                           topic_id INT NOT NULL,
                           title VARCHAR(255) NOT NULL,
                           description TEXT,
                           image_url VARCHAR(255),
                           is_public BOOLEAN DEFAULT true,
                           created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                           updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                           FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                           FOREIGN KEY (topic_id) REFERENCES topics(id)
);

-- Template Tags
CREATE TABLE template_tags (
                               template_id INT NOT NULL,
                               tag_id INT NOT NULL,
                               PRIMARY KEY (template_id, tag_id),
                               FOREIGN KEY (template_id) REFERENCES templates(id) ON DELETE CASCADE,
                               FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- Template Access (for private templates)
CREATE TABLE template_access (
                                 template_id INT NOT NULL,
                                 user_id INT NOT NULL,
                                 PRIMARY KEY (template_id, user_id),
                                 FOREIGN KEY (template_id) REFERENCES templates(id) ON DELETE CASCADE,
                                 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Questions
CREATE TABLE questions (
                           id INT AUTO_INCREMENT PRIMARY KEY,
                           template_id INT NOT NULL,
                           type ENUM('single_line', 'multiple_line', 'integer', 'checkbox') NOT NULL,
                           position INT NOT NULL,
                           title VARCHAR(255) NOT NULL,
                           description TEXT,
                           show_in_table BOOLEAN DEFAULT false,
                           FOREIGN KEY (template_id) REFERENCES templates(id) ON DELETE CASCADE
);

-- Forms (filled templates)
CREATE TABLE forms (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       template_id INT NOT NULL,
                       user_id INT NOT NULL,
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                       FOREIGN KEY (template_id) REFERENCES templates(id) ON DELETE CASCADE,
                       FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Answers
CREATE TABLE answers (
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         form_id INT NOT NULL,
                         question_id INT NOT NULL,
                         value_string VARCHAR(255),
                         value_text TEXT,
                         value_integer INT,
                         value_boolean BOOLEAN,
                         FOREIGN KEY (form_id) REFERENCES forms(id) ON DELETE CASCADE,
                         FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

-- Comments
CREATE TABLE comments (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          template_id INT NOT NULL,
                          user_id INT NOT NULL,
                          content TEXT NOT NULL,
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          FOREIGN KEY (template_id) REFERENCES templates(id) ON DELETE CASCADE,
                          FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Likes
CREATE TABLE likes (
                       template_id INT NOT NULL,
                       user_id INT NOT NULL,
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                       PRIMARY KEY (template_id, user_id),
                       FOREIGN KEY (template_id) REFERENCES templates(id) ON DELETE CASCADE,
                       FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Add fulltext search indexes
ALTER TABLE templates ADD FULLTEXT(title, description);
ALTER TABLE questions ADD FULLTEXT(title, description);
ALTER TABLE comments ADD FULLTEXT(content);