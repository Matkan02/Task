<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240403134241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tasks ADD user_task_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tasks ALTER createdat DROP DEFAULT');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_50586597D5BB1F8C FOREIGN KEY (user_task_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_50586597D5BB1F8C ON tasks (user_task_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tasks DROP CONSTRAINT FK_50586597D5BB1F8C');
        $this->addSql('DROP INDEX IDX_50586597D5BB1F8C');
        $this->addSql('ALTER TABLE tasks DROP user_task_id');
        $this->addSql('ALTER TABLE tasks ALTER createdat SET DEFAULT CURRENT_TIMESTAMP');
    }
}
