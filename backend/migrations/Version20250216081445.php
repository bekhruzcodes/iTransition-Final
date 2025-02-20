<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216081445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE forms_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE answers_id_seq CASCADE');
        $this->addSql('CREATE TABLE tags (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6FBC94265E237E06 ON tags (name)');
        $this->addSql('CREATE TABLE templates_tags (tag_id INT NOT NULL, template_id INT NOT NULL, PRIMARY KEY(tag_id, template_id))');
        $this->addSql('CREATE INDEX IDX_7121F2BFBAD26311 ON templates_tags (tag_id)');
        $this->addSql('CREATE INDEX IDX_7121F2BF5DA0FB8 ON templates_tags (template_id)');
        $this->addSql('ALTER TABLE templates_tags ADD CONSTRAINT FK_7121F2BFBAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE templates_tags ADD CONSTRAINT FK_7121F2BF5DA0FB8 FOREIGN KEY (template_id) REFERENCES templates (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forms DROP CONSTRAINT fk_template_form');
        $this->addSql('ALTER TABLE forms DROP CONSTRAINT fk_user_form');
        $this->addSql('ALTER TABLE answers DROP CONSTRAINT fk_question_answer');
        $this->addSql('ALTER TABLE answers DROP CONSTRAINT fk_form_answer');
        $this->addSql('DROP TABLE forms');
        $this->addSql('DROP TABLE answers');
        $this->addSql('ALTER TABLE questions ALTER required SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE forms_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE answers_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE forms (id SERIAL NOT NULL, template_id INT NOT NULL, user_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FD3F1BF75DA0FB8 ON forms (template_id)');
        $this->addSql('CREATE INDEX IDX_FD3F1BF7A76ED395 ON forms (user_id)');
        $this->addSql('CREATE TABLE answers (id SERIAL NOT NULL, question_id INT NOT NULL, form_id INT NOT NULL, text TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_50D0C6061E27F6BF ON answers (question_id)');
        $this->addSql('CREATE INDEX IDX_50D0C6065FF69B7D ON answers (form_id)');
        $this->addSql('ALTER TABLE forms ADD CONSTRAINT fk_template_form FOREIGN KEY (template_id) REFERENCES templates (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forms ADD CONSTRAINT fk_user_form FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE answers ADD CONSTRAINT fk_question_answer FOREIGN KEY (question_id) REFERENCES questions (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE answers ADD CONSTRAINT fk_form_answer FOREIGN KEY (form_id) REFERENCES forms (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE templates_tags DROP CONSTRAINT FK_7121F2BFBAD26311');
        $this->addSql('ALTER TABLE templates_tags DROP CONSTRAINT FK_7121F2BF5DA0FB8');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE templates_tags');
        $this->addSql('ALTER TABLE questions ALTER required DROP NOT NULL');
    }
}
