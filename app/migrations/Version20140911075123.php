<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140911075123 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_promotion_coupon_order DROP FOREIGN KEY FK_D58E3BC417B24436');
        $this->addSql('ALTER TABLE sylius_promotion_coupon_order DROP FOREIGN KEY FK_D58E3BC48D9F6D38');
        $this->addSql('ALTER TABLE sylius_promotion_coupon_order ADD CONSTRAINT FK_D58E3BC417B24436 FOREIGN KEY (promotion_coupon_id) REFERENCES sylius_promotion_coupon (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_promotion_coupon_order ADD CONSTRAINT FK_D58E3BC48D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_promotion_order DROP FOREIGN KEY FK_BF9CF6FB139DF194');
        $this->addSql('ALTER TABLE sylius_promotion_order DROP FOREIGN KEY FK_BF9CF6FB8D9F6D38');
        $this->addSql('ALTER TABLE sylius_promotion_order ADD CONSTRAINT FK_BF9CF6FB139DF194 FOREIGN KEY (promotion_id) REFERENCES sylius_promotion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_promotion_order ADD CONSTRAINT FK_BF9CF6FB8D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_inventory_unit DROP FOREIGN KEY FK_4A2769867BE036FC');
        $this->addSql('ALTER TABLE sylius_inventory_unit DROP FOREIGN KEY FK_4A276986E415FB15');
        $this->addSql('ALTER TABLE sylius_inventory_unit CHANGE order_item_id order_item_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_inventory_unit ADD CONSTRAINT FK_4A2769867BE036FC FOREIGN KEY (shipment_id) REFERENCES sylius_shipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_inventory_unit ADD CONSTRAINT FK_4A276986E415FB15 FOREIGN KEY (order_item_id) REFERENCES sylius_order_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_user DROP FOREIGN KEY FK_569A33C04D4CFF2B');
        $this->addSql('ALTER TABLE sylius_user DROP FOREIGN KEY FK_569A33C079D0C0E4');
        $this->addSql('ALTER TABLE sylius_user ADD CONSTRAINT FK_569A33C04D4CFF2B FOREIGN KEY (shipping_address_id) REFERENCES sylius_address (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_user ADD CONSTRAINT FK_569A33C079D0C0E4 FOREIGN KEY (billing_address_id) REFERENCES sylius_address (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_inventory_unit DROP FOREIGN KEY FK_4A276986E415FB15');
        $this->addSql('ALTER TABLE sylius_inventory_unit DROP FOREIGN KEY FK_4A2769867BE036FC');
        $this->addSql('ALTER TABLE sylius_inventory_unit CHANGE order_item_id order_item_id INT NOT NULL');
        $this->addSql('ALTER TABLE sylius_inventory_unit ADD CONSTRAINT FK_4A276986E415FB15 FOREIGN KEY (order_item_id) REFERENCES sylius_order_item (id)');
        $this->addSql('ALTER TABLE sylius_inventory_unit ADD CONSTRAINT FK_4A2769867BE036FC FOREIGN KEY (shipment_id) REFERENCES sylius_shipment (id)');
        $this->addSql('ALTER TABLE sylius_promotion_coupon_order DROP FOREIGN KEY FK_D58E3BC48D9F6D38');
        $this->addSql('ALTER TABLE sylius_promotion_coupon_order DROP FOREIGN KEY FK_D58E3BC417B24436');
        $this->addSql('ALTER TABLE sylius_promotion_coupon_order ADD CONSTRAINT FK_D58E3BC48D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id)');
        $this->addSql('ALTER TABLE sylius_promotion_coupon_order ADD CONSTRAINT FK_D58E3BC417B24436 FOREIGN KEY (promotion_coupon_id) REFERENCES sylius_promotion_coupon (id)');
        $this->addSql('ALTER TABLE sylius_promotion_order DROP FOREIGN KEY FK_BF9CF6FB8D9F6D38');
        $this->addSql('ALTER TABLE sylius_promotion_order DROP FOREIGN KEY FK_BF9CF6FB139DF194');
        $this->addSql('ALTER TABLE sylius_promotion_order ADD CONSTRAINT FK_BF9CF6FB8D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id)');
        $this->addSql('ALTER TABLE sylius_promotion_order ADD CONSTRAINT FK_BF9CF6FB139DF194 FOREIGN KEY (promotion_id) REFERENCES sylius_promotion (id)');
        $this->addSql('ALTER TABLE sylius_user DROP FOREIGN KEY FK_569A33C079D0C0E4');
        $this->addSql('ALTER TABLE sylius_user DROP FOREIGN KEY FK_569A33C04D4CFF2B');
        $this->addSql('ALTER TABLE sylius_user ADD CONSTRAINT FK_569A33C079D0C0E4 FOREIGN KEY (billing_address_id) REFERENCES sylius_address (id)');
        $this->addSql('ALTER TABLE sylius_user ADD CONSTRAINT FK_569A33C04D4CFF2B FOREIGN KEY (shipping_address_id) REFERENCES sylius_address (id)');
    }
}
