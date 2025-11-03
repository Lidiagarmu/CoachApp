<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251103115209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE team_invitation (id SERIAL NOT NULL, player_id INT NOT NULL, team_id INT NOT NULL, coach_id INT NOT NULL, status VARCHAR(20) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_CFC4136799E6F5DF ON team_invitation (player_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_CFC41367296CD8AE ON team_invitation (team_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_CFC413673C105691 ON team_invitation (coach_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE team_invitation ADD CONSTRAINT FK_CFC4136799E6F5DF FOREIGN KEY (player_id) REFERENCES player_profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE team_invitation ADD CONSTRAINT FK_CFC41367296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE team_invitation ADD CONSTRAINT FK_CFC413673C105691 FOREIGN KEY (coach_id) REFERENCES coach_profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE team ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NOW() NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN team.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE team_invitation DROP CONSTRAINT FK_CFC4136799E6F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE team_invitation DROP CONSTRAINT FK_CFC41367296CD8AE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE team_invitation DROP CONSTRAINT FK_CFC413673C105691
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE team_invitation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE team DROP created_at
        SQL);
    }
}
