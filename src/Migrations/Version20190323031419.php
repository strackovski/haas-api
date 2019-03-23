<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190323031419 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE cheers ADD by_user_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN cheers.by_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE cheers ADD CONSTRAINT FK_1D5D3DC9C2434 FOREIGN KEY (by_user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_1D5D3DC9C2434 ON cheers (by_user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE cheers DROP CONSTRAINT FK_1D5D3DC9C2434');
        $this->addSql('DROP INDEX IDX_1D5D3DC9C2434');
        $this->addSql('ALTER TABLE cheers DROP by_user_id');
    }
}
