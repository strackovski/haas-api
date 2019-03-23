<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190323022633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.'
        );

        $this->addSql(
            'CREATE TABLE cheers (id UUID NOT NULL, for_user_id UUID DEFAULT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, modified TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_1D5D39B5BB4B8 ON cheers (for_user_id)');
        $this->addSql('COMMENT ON COLUMN cheers.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN cheers.for_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql(
            'ALTER TABLE cheers ADD CONSTRAINT FK_1D5D39B5BB4B8 FOREIGN KEY (for_user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.'
        );

        $this->addSql('DROP TABLE cheers');
    }
}
