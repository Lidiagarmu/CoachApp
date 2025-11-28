<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251112131939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE event_images (id SERIAL NOT NULL, event_id INT NOT NULL, url VARCHAR(255) NOT NULL, uploaded_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D286C93871F7E88B ON event_images (event_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE events (id SERIAL NOT NULL, created_by_id INT NOT NULL, team_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, date DATE NOT NULL, time TIME(0) WITHOUT TIME ZONE NOT NULL, duration INT NOT NULL, location_name VARCHAR(255) NOT NULL, location_url VARCHAR(255) NOT NULL, type VARCHAR(20) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5387574AB03A8386 ON events (created_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5387574A296CD8AE ON events (team_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE games (id INT NOT NULL, team_id INT NOT NULL, opponent VARCHAR(255) NOT NULL, match_type VARCHAR(20) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_FF232B31296CD8AE ON games (team_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trainings (id INT NOT NULL, coach_id INT NOT NULL, training_type VARCHAR(20) NOT NULL, focus_area VARCHAR(50) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_66DC43303C105691 ON trainings (coach_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event_images ADD CONSTRAINT FK_D286C93871F7E88B FOREIGN KEY (event_id) REFERENCES events (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE events ADD CONSTRAINT FK_5387574AB03A8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE events ADD CONSTRAINT FK_5387574A296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE games ADD CONSTRAINT FK_FF232B31BF396750 FOREIGN KEY (id) REFERENCES events (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE games ADD CONSTRAINT FK_FF232B31296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainings ADD CONSTRAINT FK_66DC4330BF396750 FOREIGN KEY (id) REFERENCES events (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainings ADD CONSTRAINT FK_66DC43303C105691 FOREIGN KEY (coach_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event_images DROP CONSTRAINT FK_D286C93871F7E88B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE events DROP CONSTRAINT FK_5387574AB03A8386
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE events DROP CONSTRAINT FK_5387574A296CD8AE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE games DROP CONSTRAINT FK_FF232B31BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE games DROP CONSTRAINT FK_FF232B31296CD8AE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainings DROP CONSTRAINT FK_66DC4330BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainings DROP CONSTRAINT FK_66DC43303C105691
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE event_images
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE events
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE games
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trainings
        SQL);
    }
}
