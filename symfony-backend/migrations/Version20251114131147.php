<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251114131147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE event_player_profile (event_id INT NOT NULL, player_profile_id INT NOT NULL, PRIMARY KEY(event_id, player_profile_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3FE7E33971F7E88B ON event_player_profile (event_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3FE7E339935F9685 ON event_player_profile (player_profile_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event_player_profile ADD CONSTRAINT FK_3FE7E33971F7E88B FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event_player_profile ADD CONSTRAINT FK_3FE7E339935F9685 FOREIGN KEY (player_profile_id) REFERENCES player_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE events DROP CONSTRAINT fk_5387574a99e6f5df
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_5387574a99e6f5df
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE events DROP player_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event_player_profile DROP CONSTRAINT FK_3FE7E33971F7E88B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event_player_profile DROP CONSTRAINT FK_3FE7E339935F9685
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE event_player_profile
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE events ADD player_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE events ADD CONSTRAINT fk_5387574a99e6f5df FOREIGN KEY (player_id) REFERENCES player_profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_5387574a99e6f5df ON events (player_id)
        SQL);
    }
}
