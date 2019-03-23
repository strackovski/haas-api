<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190323082546 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE cheers ADD parent_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN cheers.parent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE cheers ADD CONSTRAINT FK_1D5D3727ACA70 FOREIGN KEY (parent_id) REFERENCES cheers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_1D5D3727ACA70 ON cheers (parent_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE cheers DROP CONSTRAINT FK_1D5D3727ACA70');
        $this->addSql('DROP INDEX IDX_1D5D3727ACA70');
        $this->addSql('ALTER TABLE cheers DROP parent_id');
    }
}
