<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251113173136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE trainings DROP CONSTRAINT FK_66DC4330BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainings ADD CONSTRAINT FK_66DC4330BF396750 FOREIGN KEY (id) REFERENCES events (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainings DROP CONSTRAINT fk_66dc4330bf396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainings ADD CONSTRAINT fk_66dc4330bf396750 FOREIGN KEY (id) REFERENCES events (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }
}
