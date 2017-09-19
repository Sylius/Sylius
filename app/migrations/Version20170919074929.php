<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170919074929 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_customer_group ADD tax_category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_customer_group ADD CONSTRAINT FK_7FCF9B059DF894ED FOREIGN KEY (tax_category_id) REFERENCES sylius_customer_tax_category (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_7FCF9B059DF894ED ON sylius_customer_group (tax_category_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_customer_group DROP FOREIGN KEY FK_7FCF9B059DF894ED');
        $this->addSql('DROP INDEX IDX_7FCF9B059DF894ED ON sylius_customer_group');
        $this->addSql('ALTER TABLE sylius_customer_group DROP tax_category_id');
    }
}
