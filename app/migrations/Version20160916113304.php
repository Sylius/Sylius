<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160916113304 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_order DROP INDEX UNIQ_6196A1F94D4CFF2B, ADD INDEX IDX_6196A1F94D4CFF2B (shipping_address_id)');
        $this->addSql('ALTER TABLE sylius_order DROP INDEX UNIQ_6196A1F979D0C0E4, ADD INDEX IDX_6196A1F979D0C0E4 (billing_address_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_order DROP INDEX IDX_6196A1F94D4CFF2B, ADD UNIQUE INDEX UNIQ_6196A1F94D4CFF2B (shipping_address_id)');
        $this->addSql('ALTER TABLE sylius_order DROP INDEX IDX_6196A1F979D0C0E4, ADD UNIQUE INDEX UNIQ_6196A1F979D0C0E4 (billing_address_id)');
    }
}
