<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251014103748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE coach_profile (id SERIAL NOT NULL, user_account_id INT NOT NULL, team_name VARCHAR(255) NOT NULL, years_experience INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_3A8742473C0C9956 ON coach_profile (user_account_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE coach_profile ADD CONSTRAINT FK_3A8742473C0C9956 FOREIGN KEY (user_account_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE coach_profile DROP CONSTRAINT FK_3A8742473C0C9956
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE coach_profile
        SQL);
    }
}
