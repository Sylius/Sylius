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
class Version20151229134904 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_order_item_unit (id INT AUTO_INCREMENT NOT NULL, order_item_id INT NOT NULL, shipment_id INT DEFAULT NULL, total INT NOT NULL, adjustments_total INT NOT NULL, inventory_state VARCHAR(255) NOT NULL, shipping_state VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_82BF226EE415FB15 (order_item_id), INDEX IDX_82BF226E7BE036FC (shipment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_order_item_unit ADD CONSTRAINT FK_82BF226EE415FB15 FOREIGN KEY (order_item_id) REFERENCES sylius_order_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_order_item_unit ADD CONSTRAINT FK_82BF226E7BE036FC FOREIGN KEY (shipment_id) REFERENCES sylius_shipment (id) ON DELETE SET NULL');
        $this->addSql('DROP TABLE sylius_inventory_unit');
        $this->addSql('ALTER TABLE sylius_adjustment ADD order_item_unit_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_adjustment ADD CONSTRAINT FK_ACA6E0F2F720C233 FOREIGN KEY (order_item_unit_id) REFERENCES sylius_order_item_unit (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_ACA6E0F2F720C233 ON sylius_adjustment (order_item_unit_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_adjustment DROP FOREIGN KEY FK_ACA6E0F2F720C233');
        $this->addSql('CREATE TABLE sylius_inventory_unit (id INT AUTO_INCREMENT NOT NULL, shipment_id INT DEFAULT NULL, order_item_id INT NOT NULL, stockable_id INT NOT NULL, inventory_state VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, shipping_state VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, INDEX IDX_4A276986FBE8234 (stockable_id), INDEX IDX_4A276986E415FB15 (order_item_id), INDEX IDX_4A2769867BE036FC (shipment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_inventory_unit ADD CONSTRAINT FK_4A2769867BE036FC FOREIGN KEY (shipment_id) REFERENCES sylius_shipment (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE sylius_inventory_unit ADD CONSTRAINT FK_4A276986E415FB15 FOREIGN KEY (order_item_id) REFERENCES sylius_order_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_inventory_unit ADD CONSTRAINT FK_4A276986FBE8234 FOREIGN KEY (stockable_id) REFERENCES sylius_product_variant (id)');
        $this->addSql('DROP TABLE sylius_order_item_unit');
        $this->addSql('DROP INDEX IDX_ACA6E0F2F720C233 ON sylius_adjustment');
        $this->addSql('ALTER TABLE sylius_adjustment DROP order_item_unit_id');
    }
}
