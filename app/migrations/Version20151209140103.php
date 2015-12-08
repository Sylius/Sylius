<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151209140103 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_E74256BF4B80EAC0 ON sylius_country');
        $this->addSql('DROP INDEX IDX_E74256BF4B80EAC0 ON sylius_country');
        $this->addSql('ALTER TABLE sylius_country ADD code VARCHAR(255) NOT NULL, DROP iso_name');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E74256BF77153098 ON sylius_country (code)');
        $this->addSql('CREATE INDEX IDX_E74256BF77153098 ON sylius_country (code)');
        $this->addSql('ALTER TABLE sylius_province ADD code VARCHAR(255) NOT NULL, DROP iso_name');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B5618FE477153098 ON sylius_province (code)');
        $this->addSql('ALTER TABLE sylius_zone ADD code VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7BE2258E77153098 ON sylius_zone (code)');
        $this->addSql('ALTER TABLE sylius_zone_member ADD code VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E8B5ABF377153098 ON sylius_zone_member (code)');
        $this->addSql('ALTER TABLE sylius_address DROP FOREIGN KEY FK_B97FF058E946114A');
        $this->addSql('ALTER TABLE sylius_address DROP FOREIGN KEY FK_B97FF058F92F3E70');
        $this->addSql('DROP INDEX IDX_B97FF058F92F3E70 ON sylius_address');
        $this->addSql('DROP INDEX IDX_B97FF058E946114A ON sylius_address');
        $this->addSql('ALTER TABLE sylius_address ADD country_code VARCHAR(255) NOT NULL, ADD province_code VARCHAR(255) DEFAULT NULL, DROP province_id, DROP country_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_address ADD province_id INT DEFAULT NULL, ADD country_id INT DEFAULT NULL, DROP country_code, DROP province_code');
        $this->addSql('ALTER TABLE sylius_address ADD CONSTRAINT FK_B97FF058E946114A FOREIGN KEY (province_id) REFERENCES sylius_province (id)');
        $this->addSql('ALTER TABLE sylius_address ADD CONSTRAINT FK_B97FF058F92F3E70 FOREIGN KEY (country_id) REFERENCES sylius_country (id)');
        $this->addSql('CREATE INDEX IDX_B97FF058F92F3E70 ON sylius_address (country_id)');
        $this->addSql('CREATE INDEX IDX_B97FF058E946114A ON sylius_address (province_id)');
        $this->addSql('DROP INDEX UNIQ_E74256BF77153098 ON sylius_country');
        $this->addSql('DROP INDEX IDX_E74256BF77153098 ON sylius_country');
        $this->addSql('ALTER TABLE sylius_country ADD iso_name VARCHAR(3) NOT NULL COLLATE utf8_unicode_ci, DROP code');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E74256BF4B80EAC0 ON sylius_country (iso_name)');
        $this->addSql('CREATE INDEX IDX_E74256BF4B80EAC0 ON sylius_country (iso_name)');
        $this->addSql('DROP INDEX UNIQ_B5618FE477153098 ON sylius_province');
        $this->addSql('ALTER TABLE sylius_province ADD iso_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP code');
        $this->addSql('DROP INDEX UNIQ_7BE2258E77153098 ON sylius_zone');
        $this->addSql('ALTER TABLE sylius_zone DROP code');
        $this->addSql('DROP INDEX UNIQ_E8B5ABF377153098 ON sylius_zone_member');
        $this->addSql('ALTER TABLE sylius_zone_member DROP code');
    }
}
