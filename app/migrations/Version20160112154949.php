<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160112154949 extends AbstractMigration
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
        $this->addSql('ALTER TABLE `sylius_country` CHANGE `iso_name` `code` VARCHAR(2) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E74256BF77153098 ON sylius_country (code)');
        $this->addSql('CREATE INDEX IDX_E74256BF77153098 ON sylius_country (code)');
        $this->addSql('ALTER TABLE `sylius_province` CHANGE `iso_name` `code` VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B5618FE477153098 ON sylius_province (code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B5618FE4F92F3E705E237E06 ON sylius_province (country_id, name)');
        $this->addSql('ALTER TABLE sylius_zone ADD code VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE sylius_zone s,
                           (SELECT @n := 0) m
                           SET s.`code` = CONCAT("Z", @n := @n + 1)         
                      ');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7BE2258E77153098 ON sylius_zone (code)');
        $this->addSql('ALTER TABLE sylius_zone_member DROP FOREIGN KEY FK_E8B5ABF39F2C3FAB');
        $this->addSql('ALTER TABLE sylius_zone_member DROP FOREIGN KEY FK_E8B5ABF3E946114A');
        $this->addSql('ALTER TABLE sylius_zone_member DROP FOREIGN KEY FK_E8B5ABF3F92F3E70');
        $this->addSql('DROP INDEX IDX_E8B5ABF3F92F3E70 ON sylius_zone_member');
        $this->addSql('DROP INDEX IDX_E8B5ABF3E946114A ON sylius_zone_member');
        $this->addSql('DROP INDEX IDX_E8B5ABF39F2C3FAB ON sylius_zone_member');
        $this->addSql('ALTER TABLE sylius_zone_member ADD code VARCHAR(255) NOT NULL, DROP zone_id, DROP province_id, DROP country_id, DROP type');
        $this->addSql('UPDATE sylius_zone_member s,
                           (SELECT @n := 0) m
                           SET s.`code` = CONCAT("ZM", @n := @n + 1)         
                      ');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E8B5ABF34B0E929B77153098 ON sylius_zone_member (belongs_to, code)');
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
        $this->addSql('DROP INDEX UNIQ_B5618FE4F92F3E705E237E06 ON sylius_province');
        $this->addSql('ALTER TABLE sylius_province ADD iso_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP code');
        $this->addSql('DROP INDEX UNIQ_7BE2258E77153098 ON sylius_zone');
        $this->addSql('ALTER TABLE sylius_zone DROP code');
        $this->addSql('DROP INDEX UNIQ_E8B5ABF34B0E929B77153098 ON sylius_zone_member');
        $this->addSql('ALTER TABLE sylius_zone_member ADD zone_id INT DEFAULT NULL, ADD province_id INT DEFAULT NULL, ADD country_id INT DEFAULT NULL, ADD type VARCHAR(8) NOT NULL COLLATE utf8_unicode_ci, DROP code');
        $this->addSql('ALTER TABLE sylius_zone_member ADD CONSTRAINT FK_E8B5ABF39F2C3FAB FOREIGN KEY (zone_id) REFERENCES sylius_zone (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_zone_member ADD CONSTRAINT FK_E8B5ABF3E946114A FOREIGN KEY (province_id) REFERENCES sylius_province (id)');
        $this->addSql('ALTER TABLE sylius_zone_member ADD CONSTRAINT FK_E8B5ABF3F92F3E70 FOREIGN KEY (country_id) REFERENCES sylius_country (id)');
        $this->addSql('CREATE INDEX IDX_E8B5ABF3F92F3E70 ON sylius_zone_member (country_id)');
        $this->addSql('CREATE INDEX IDX_E8B5ABF3E946114A ON sylius_zone_member (province_id)');
        $this->addSql('CREATE INDEX IDX_E8B5ABF39F2C3FAB ON sylius_zone_member (zone_id)');
    }
}
