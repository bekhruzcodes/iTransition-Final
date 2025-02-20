<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250214190024 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // Create update_updated_at function
        $this->addSql('CREATE OR REPLACE FUNCTION update_updated_at_column()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            $$ language "plpgsql"');

        // Create Forms table
        $this->addSql('CREATE TABLE forms (
            id SERIAL PRIMARY KEY,
            template_id INT NOT NULL,
            user_id INT NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_template_form
                FOREIGN KEY (template_id) 
                REFERENCES templates(id)
                ON DELETE CASCADE,
            CONSTRAINT fk_user_form
                FOREIGN KEY (user_id) 
                REFERENCES "user"(id)
                ON DELETE CASCADE
        )');

        $this->addSql('CREATE TRIGGER update_forms_updated_at
            BEFORE UPDATE ON forms
            FOR EACH ROW
            EXECUTE FUNCTION update_updated_at_column()');

        // Create Questions table
        $this->addSql('CREATE TABLE questions (
            id SERIAL PRIMARY KEY,
            template_id INT NOT NULL,
            text VARCHAR(255) NOT NULL,
            type VARCHAR(50) NOT NULL,
            required BOOLEAN DEFAULT true,
            order_num INT NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_template_question
                FOREIGN KEY (template_id) 
                REFERENCES templates(id)
                ON DELETE CASCADE
        )');

        $this->addSql('CREATE TRIGGER update_questions_updated_at
            BEFORE UPDATE ON questions
            FOR EACH ROW
            EXECUTE FUNCTION update_updated_at_column()');

        // Create Answers table
        $this->addSql('CREATE TABLE answers (
            id SERIAL PRIMARY KEY,
            question_id INT NOT NULL,
            form_id INT NOT NULL,
            text TEXT,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_question_answer
                FOREIGN KEY (question_id) 
                REFERENCES questions(id)
                ON DELETE CASCADE,
            CONSTRAINT fk_form_answer
                FOREIGN KEY (form_id) 
                REFERENCES forms(id)
                ON DELETE CASCADE
        )');

        $this->addSql('CREATE TRIGGER update_answers_updated_at
            BEFORE UPDATE ON answers
            FOR EACH ROW
            EXECUTE FUNCTION update_updated_at_column()');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TRIGGER IF EXISTS update_answers_updated_at ON answers');
        $this->addSql('DROP TRIGGER IF EXISTS update_questions_updated_at ON questions');
        $this->addSql('DROP TRIGGER IF EXISTS update_forms_updated_at ON forms');
        $this->addSql('DROP FUNCTION IF EXISTS update_updated_at_column()');
        $this->addSql('DROP TABLE IF EXISTS answers');
        $this->addSql('DROP TABLE IF EXISTS questions');
        $this->addSql('DROP TABLE IF EXISTS forms');
    }
}