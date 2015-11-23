<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151126085942 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sylius_payment_config');
        $this->addSql('ALTER TABLE sylius_product_attribute_value CHANGE text text_value LONGTEXT DEFAULT NULL, CHANGE boolean boolean_value TINYINT(1) DEFAULT NULL, CHANGE datetime datetime_value DATETIME DEFAULT NULL');
        $this->addSql('DROP INDEX fulltext_search_idx ON sylius_search_index');
        $this->addSql('CREATE INDEX fulltext_search_idx ON sylius_search_index (item_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_payment_config (id INT AUTO_INCREMENT NOT NULL, config LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\', payment_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, factory_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_product_attribute_value CHANGE text_value text LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE boolean_value boolean TINYINT(1) DEFAULT NULL, CHANGE datetime_value datetime DATETIME DEFAULT NULL');
        $this->addSql('DROP INDEX fulltext_search_idx ON sylius_search_index');
        $this->addSql('CREATE FULLTEXT INDEX fulltext_search_idx ON sylius_search_index (value)');
    }
}
