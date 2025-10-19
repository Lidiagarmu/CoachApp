<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251019111002 extends AbstractMigration
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
            CREATE TABLE player_profile (id SERIAL NOT NULL, player_account_id INT NOT NULL, coach_id INT DEFAULT NULL, team_id INT DEFAULT NULL, position VARCHAR(100) NOT NULL, number INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_E0A3554AC4CEDB87 ON player_profile (player_account_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E0A3554A3C105691 ON player_profile (coach_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E0A3554A296CD8AE ON player_profile (team_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE team (id SERIAL NOT NULL, coach_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_C4E0A61F3C105691 ON team (coach_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, full_name VARCHAR(100) NOT NULL, nickname VARCHAR(50) DEFAULT NULL, age INT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN messenger_messages.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN messenger_messages.available_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN messenger_messages.delivered_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
                BEGIN
                    PERFORM pg_notify('messenger_messages', NEW.queue_name::text);
                    RETURN NEW;
                END;
            $$ LANGUAGE plpgsql;
        SQL);
        $this->addSql(<<<'SQL'
            DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE coach_profile ADD CONSTRAINT FK_3A8742473C0C9956 FOREIGN KEY (user_account_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE player_profile ADD CONSTRAINT FK_E0A3554AC4CEDB87 FOREIGN KEY (player_account_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE player_profile ADD CONSTRAINT FK_E0A3554A3C105691 FOREIGN KEY (coach_id) REFERENCES coach_profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE player_profile ADD CONSTRAINT FK_E0A3554A296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F3C105691 FOREIGN KEY (coach_id) REFERENCES coach_profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE
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
            ALTER TABLE player_profile DROP CONSTRAINT FK_E0A3554AC4CEDB87
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE player_profile DROP CONSTRAINT FK_E0A3554A3C105691
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE player_profile DROP CONSTRAINT FK_E0A3554A296CD8AE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE team DROP CONSTRAINT FK_C4E0A61F3C105691
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE coach_profile
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE player_profile
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE team
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "user"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
