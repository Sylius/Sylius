<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160418191631 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_customer ADD vat_number VARCHAR(255) DEFAULT NULL, ADD reseller_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_tax_rate ADD applied_to_individuals TINYINT(1) DEFAULT \'1\' NOT NULL, ADD applied_to_entrepreneurs_and_resellers TINYINT(1) DEFAULT \'1\' NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_customer DROP vat_number, DROP reseller_id');
        $this->addSql('ALTER TABLE sylius_tax_rate DROP applied_to_individuals, DROP applied_to_entrepreneurs_and_resellers');
    }
}
