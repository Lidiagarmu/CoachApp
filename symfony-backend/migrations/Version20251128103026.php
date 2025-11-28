<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251128103026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE events_players (event_id INT NOT NULL, player_profile_id INT NOT NULL, PRIMARY KEY(event_id, player_profile_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_702F3ABC71F7E88B ON events_players (event_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_702F3ABC935F9685 ON events_players (player_profile_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE events_players ADD CONSTRAINT FK_702F3ABC71F7E88B FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE events_players ADD CONSTRAINT FK_702F3ABC935F9685 FOREIGN KEY (player_profile_id) REFERENCES player_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event_player_profile DROP CONSTRAINT fk_3fe7e33971f7e88b
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event_player_profile DROP CONSTRAINT fk_3fe7e339935f9685
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE event_player_profile
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE event_player_profile (event_id INT NOT NULL, player_profile_id INT NOT NULL, PRIMARY KEY(event_id, player_profile_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_3fe7e339935f9685 ON event_player_profile (player_profile_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_3fe7e33971f7e88b ON event_player_profile (event_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event_player_profile ADD CONSTRAINT fk_3fe7e33971f7e88b FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event_player_profile ADD CONSTRAINT fk_3fe7e339935f9685 FOREIGN KEY (player_profile_id) REFERENCES player_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE events_players DROP CONSTRAINT FK_702F3ABC71F7E88B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE events_players DROP CONSTRAINT FK_702F3ABC935F9685
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE events_players
        SQL);
    }
}
