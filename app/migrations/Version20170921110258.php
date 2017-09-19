<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170921110258 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_customer_tax_category (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_B1ED81CB77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_channel ADD default_customer_tax_category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_channel ADD CONSTRAINT FK_16C8119EDC7BD4C0 FOREIGN KEY (default_customer_tax_category_id) REFERENCES sylius_customer_tax_category (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_16C8119EDC7BD4C0 ON sylius_channel (default_customer_tax_category_id)');
        $this->addSql('ALTER TABLE sylius_customer_group ADD tax_category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_customer_group ADD CONSTRAINT FK_7FCF9B059DF894ED FOREIGN KEY (tax_category_id) REFERENCES sylius_customer_tax_category (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_7FCF9B059DF894ED ON sylius_customer_group (tax_category_id)');
        $this->addSql('ALTER TABLE sylius_tax_rate ADD customer_tax_category_id INT NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_channel DROP FOREIGN KEY FK_16C8119EDC7BD4C0');
        $this->addSql('ALTER TABLE sylius_customer_group DROP FOREIGN KEY FK_7FCF9B059DF894ED');
        $this->addSql('ALTER TABLE sylius_tax_rate DROP FOREIGN KEY FK_3CD86B2EE6D0D277');
        $this->addSql('DROP TABLE sylius_customer_tax_category');
        $this->addSql('DROP INDEX IDX_16C8119EDC7BD4C0 ON sylius_channel');
        $this->addSql('ALTER TABLE sylius_channel DROP default_customer_tax_category_id');
        $this->addSql('DROP INDEX IDX_7FCF9B059DF894ED ON sylius_customer_group');
        $this->addSql('ALTER TABLE sylius_tax_rate DROP customer_tax_category_id');
    }
}
