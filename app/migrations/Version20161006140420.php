<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161006140420 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sylius_customer_group');
        $this->addSql('ALTER TABLE sylius_customer ADD group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_customer ADD CONSTRAINT FK_7E82D5E6FE54D947 FOREIGN KEY (group_id) REFERENCES sylius_group (id)');
        $this->addSql('CREATE INDEX IDX_7E82D5E6FE54D947 ON sylius_customer (group_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_customer_group (customer_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_7FCF9B059395C3F3 (customer_id), INDEX IDX_7FCF9B05FE54D947 (group_id), PRIMARY KEY(customer_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_customer_group ADD CONSTRAINT FK_7FCF9B059395C3F3 FOREIGN KEY (customer_id) REFERENCES sylius_customer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_customer_group ADD CONSTRAINT FK_7FCF9B05FE54D947 FOREIGN KEY (group_id) REFERENCES sylius_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_customer DROP FOREIGN KEY FK_7E82D5E6FE54D947');
        $this->addSql('DROP INDEX IDX_7E82D5E6FE54D947 ON sylius_customer');
        $this->addSql('ALTER TABLE sylius_customer DROP group_id');
    }
}
