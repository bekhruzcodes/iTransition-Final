<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216175428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE question_options (id SERIAL NOT NULL, question_id INT NOT NULL, text VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, order_num INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DEE92F9A1E27F6BF ON question_options (question_id)');
        $this->addSql('ALTER TABLE question_options ADD CONSTRAINT FK_DEE92F9A1E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE question_options DROP CONSTRAINT FK_DEE92F9A1E27F6BF');
        $this->addSql('DROP TABLE question_options');
    }
}
