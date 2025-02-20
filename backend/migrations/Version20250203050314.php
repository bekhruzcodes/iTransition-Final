<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250203050314 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration for PostgreSQL with proper table naming';
    }

    public function up(Schema $schema): void
    {
        // Modify the user table creation for PostgreSQL
        $this->addSql('CREATE TABLE "user" (
            id SERIAL NOT NULL, 
            name VARCHAR(180) NOT NULL,
            email VARCHAR(180) NOT NULL, 
            password VARCHAR(255) NOT NULL, 
            status VARCHAR(50) NOT NULL DEFAULT \'active\', 
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
            CONSTRAINT user_email_unique UNIQUE (email), 
            PRIMARY KEY (id)
        )');

        // Modify the messenger_messages table for PostgreSQL
        $this->addSql('CREATE TABLE messenger_messages (
            id BIGSERIAL NOT NULL, 
            body TEXT NOT NULL, 
            headers TEXT NOT NULL, 
            queue_name VARCHAR(190) NOT NULL, 
            created_at TIMESTAMP NOT NULL, 
            available_at TIMESTAMP NOT NULL, 
            delivered_at TIMESTAMP DEFAULT NULL, 
            PRIMARY KEY (id)
        )');

        // Add indexes
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
