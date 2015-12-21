<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160115144017 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_administrative_area (id INT AUTO_INCREMENT NOT NULL, country_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_DA0122FD77153098 (code), INDEX IDX_DA0122FDF92F3E70 (country_id), UNIQUE INDEX UNIQ_DA0122FDF92F3E705E237E06 (country_id, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_administrative_area ADD CONSTRAINT FK_DA0122FDF92F3E70 FOREIGN KEY (country_id) REFERENCES sylius_country (id)');
        $this->addSql('DROP TABLE sylius_province');
        $this->addSql('ALTER TABLE sylius_zone CHANGE type type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE sylius_address ADD organization VARCHAR(255) DEFAULT NULL, ADD locality VARCHAR(255) NOT NULL, ADD dependent_locality VARCHAR(255) DEFAULT NULL, ADD first_address_line VARCHAR(255) NOT NULL, ADD second_address_line VARCHAR(255) DEFAULT NULL, ADD administrative_area_code VARCHAR(255) DEFAULT NULL, DROP street, DROP company, DROP city, DROP province_code');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_province (id INT AUTO_INCREMENT NOT NULL, country_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX UNIQ_B5618FE477153098 (code), UNIQUE INDEX UNIQ_B5618FE4F92F3E705E237E06 (country_id, name), INDEX IDX_B5618FE4F92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_province ADD CONSTRAINT FK_B5618FE4F92F3E70 FOREIGN KEY (country_id) REFERENCES sylius_country (id)');
        $this->addSql('DROP TABLE sylius_administrative_area');
        $this->addSql('ALTER TABLE sylius_address ADD street VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD company VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD city VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD province_code VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP organization, DROP locality, DROP dependent_locality, DROP first_address_line, DROP second_address_line, DROP administrative_area_code');
        $this->addSql('ALTER TABLE sylius_zone CHANGE type type VARCHAR(8) NOT NULL COLLATE utf8_unicode_ci');
    }
}
