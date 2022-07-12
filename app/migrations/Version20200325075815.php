<?php

declare(strict_types=1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200325075815 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(!is_a($this->connection->getDatabasePlatform(), \Doctrine\DBAL\Platforms\MySqlPlatform::class, true), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_taxon ADD enabled TINYINT(1) NOT NULL');
        $this->addSql('UPDATE sylius_taxon SET enabled = 1');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(!is_a($this->connection->getDatabasePlatform(), \Doctrine\DBAL\Platforms\MySqlPlatform::class, true), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_taxon DROP enabled');
    }
}
