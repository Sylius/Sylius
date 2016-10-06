<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161006140813 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_customer DROP FOREIGN KEY FK_7E82D5E6FE54D947');
        $this->addSql('CREATE TABLE sylius_customer_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE sylius_group');
        $this->addSql('DROP INDEX IDX_7E82D5E6FE54D947 ON sylius_customer');
        $this->addSql('ALTER TABLE sylius_customer CHANGE group_id customer_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_customer ADD CONSTRAINT FK_7E82D5E6D2919A68 FOREIGN KEY (customer_group_id) REFERENCES sylius_customer_group (id)');
        $this->addSql('CREATE INDEX IDX_7E82D5E6D2919A68 ON sylius_customer (customer_group_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_customer DROP FOREIGN KEY FK_7E82D5E6D2919A68');
        $this->addSql('CREATE TABLE sylius_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE sylius_customer_group');
        $this->addSql('DROP INDEX IDX_7E82D5E6D2919A68 ON sylius_customer');
        $this->addSql('ALTER TABLE sylius_customer CHANGE customer_group_id group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_customer ADD CONSTRAINT FK_7E82D5E6FE54D947 FOREIGN KEY (group_id) REFERENCES sylius_group (id)');
        $this->addSql('CREATE INDEX IDX_7E82D5E6FE54D947 ON sylius_customer (group_id)');
    }
}
