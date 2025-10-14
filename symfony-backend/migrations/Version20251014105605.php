<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251014105605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE player_profile (id SERIAL NOT NULL, player_account_id INT NOT NULL, position VARCHAR(100) NOT NULL, number INT NOT NULL, team VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_E0A3554AC4CEDB87 ON player_profile (player_account_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE player_profile ADD CONSTRAINT FK_E0A3554AC4CEDB87 FOREIGN KEY (player_account_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE player_profile DROP CONSTRAINT FK_E0A3554AC4CEDB87
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE player_profile
        SQL);
    }
}
