<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration for Topics and Templates tables
 */
final class Version20250204211359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates topics and templates tables';
    }

    public function up(Schema $schema): void
    {
        // Create topics table
        $this->addSql('CREATE TABLE topics (
            id SERIAL NOT NULL, 
            name VARCHAR(255) NOT NULL, 
            PRIMARY KEY (id)
        )');

        // Create templates table
        $this->addSql('CREATE TABLE templates (
            id SERIAL NOT NULL, 
            user_id INT NOT NULL, 
            topic_id INT NOT NULL, 
            title VARCHAR(255) NOT NULL, 
            description TEXT NOT NULL, 
            image_url VARCHAR(255) DEFAULT NULL, 
            is_public BOOLEAN NOT NULL DEFAULT TRUE, 
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
            PRIMARY KEY (id)
        )');

        // Add foreign keys
        $this->addSql('ALTER TABLE templates ADD CONSTRAINT fk_templates_user FOREIGN KEY (user_id) REFERENCES "user"(id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE templates ADD CONSTRAINT fk_templates_topic FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE');

        // Add indexes
        $this->addSql('CREATE INDEX idx_templates_user ON templates (user_id)');
        $this->addSql('CREATE INDEX idx_templates_topic ON templates (topic_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE templates');
        $this->addSql('DROP TABLE topics');
    }
}
