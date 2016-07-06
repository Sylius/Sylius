<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160524085632 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('RENAME TABLE sylius_metadata TO sylius_metadata_old');

        $this->addSql('CREATE TABLE `sylius_metadata` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `metadata` longtext COLLATE utf8_unicode_ci NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addSql('CREATE UNIQUE INDEX type_code_idx ON sylius_metadata (type, code)');

        $this->addSql('INSERT INTO sylius_metadata (code, type, metadata) (SELECT id, "page", metadata FROM sylius_metadata_old)');

        $this->addSql('DROP TABLE sylius_metadata_old');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('RENAME TABLE sylius_metadata TO sylius_metadata_new');

        $this->addSql('CREATE TABLE sylius_metadata (id VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, metadata LONGTEXT NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('INSERT INTO sylius_metadata (id, metadata) (SELECT code, metadata FROM sylius_metadata_new)');

        $this->addSql('DROP TABLE sylius_metadata_new');
    }
}
