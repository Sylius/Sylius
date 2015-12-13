<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151213135808 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_adjustment DROP FOREIGN KEY FK_ACA6E0F2E415FB15');
        $this->addSql('DROP INDEX IDX_ACA6E0F2E415FB15 ON sylius_adjustment');
        $this->addSql('ALTER TABLE sylius_adjustment DROP order_item_id');
        $this->addSql('ALTER TABLE sylius_adjustment ADD inventory_unit_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_adjustment ADD CONSTRAINT FK_ACA6E0F2B9B9D6F1 FOREIGN KEY (inventory_unit_id) REFERENCES sylius_inventory_unit (id)');
        $this->addSql('CREATE INDEX IDX_ACA6E0F2B9B9D6F1 ON sylius_adjustment (inventory_unit_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_adjustment DROP FOREIGN KEY FK_ACA6E0F2B9B9D6F1');
        $this->addSql('DROP INDEX IDX_ACA6E0F2B9B9D6F1 ON sylius_adjustment');
        $this->addSql('ALTER TABLE sylius_adjustment DROP inventory_unit_id');
        $this->addSql('ALTER TABLE sylius_adjustment ADD order_item_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_adjustment ADD CONSTRAINT FK_ACA6E0F2E415FB15 FOREIGN KEY (order_item_id) REFERENCES sylius_order_item (id)');
        $this->addSql('CREATE INDEX IDX_ACA6E0F2E415FB15 ON sylius_adjustment (order_item_id)');
    }
}
