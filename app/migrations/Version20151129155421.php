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
class Version20151129155421 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_adjustment DROP FOREIGN KEY FK_ACA6E0F28D9F6D38');
        $this->addSql('ALTER TABLE sylius_adjustment DROP FOREIGN KEY FK_ACA6E0F2E415FB15');
        $this->addSql('ALTER TABLE sylius_adjustment ADD CONSTRAINT FK_ACA6E0F28D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_adjustment ADD CONSTRAINT FK_ACA6E0F2E415FB15 FOREIGN KEY (order_item_id) REFERENCES sylius_order_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_inventory_unit DROP FOREIGN KEY FK_4A276986E415FB15');
        $this->addSql('ALTER TABLE sylius_inventory_unit ADD CONSTRAINT FK_4A276986E415FB15 FOREIGN KEY (order_item_id) REFERENCES sylius_order_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_promotion_order DROP FOREIGN KEY FK_BF9CF6FB8D9F6D38');
        $this->addSql('ALTER TABLE sylius_promotion_order ADD CONSTRAINT FK_BF9CF6FB8D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_order_item DROP FOREIGN KEY FK_77B587ED8D9F6D38');
        $this->addSql('ALTER TABLE sylius_order_item ADD CONSTRAINT FK_77B587ED8D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_promotion_order_item DROP FOREIGN KEY FK_49838ED1E415FB15');
        $this->addSql('ALTER TABLE sylius_promotion_order_item ADD CONSTRAINT FK_49838ED1E415FB15 FOREIGN KEY (order_item_id) REFERENCES sylius_order_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_payment DROP FOREIGN KEY FK_D9191BD48D9F6D38');
        $this->addSql('ALTER TABLE sylius_payment ADD CONSTRAINT FK_D9191BD48D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_shipment DROP FOREIGN KEY FK_FD707B338D9F6D38');
        $this->addSql('ALTER TABLE sylius_shipment ADD CONSTRAINT FK_FD707B338D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_adjustment DROP FOREIGN KEY FK_ACA6E0F28D9F6D38');
        $this->addSql('ALTER TABLE sylius_adjustment DROP FOREIGN KEY FK_ACA6E0F2E415FB15');
        $this->addSql('ALTER TABLE sylius_adjustment ADD CONSTRAINT FK_ACA6E0F28D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id)');
        $this->addSql('ALTER TABLE sylius_adjustment ADD CONSTRAINT FK_ACA6E0F2E415FB15 FOREIGN KEY (order_item_id) REFERENCES sylius_order_item (id)');
        $this->addSql('ALTER TABLE sylius_inventory_unit DROP FOREIGN KEY FK_4A276986E415FB15');
        $this->addSql('ALTER TABLE sylius_inventory_unit ADD CONSTRAINT FK_4A276986E415FB15 FOREIGN KEY (order_item_id) REFERENCES sylius_order_item (id)');
        $this->addSql('ALTER TABLE sylius_order_item DROP FOREIGN KEY FK_77B587ED8D9F6D38');
        $this->addSql('ALTER TABLE sylius_order_item ADD CONSTRAINT FK_77B587ED8D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id)');
        $this->addSql('ALTER TABLE sylius_payment DROP FOREIGN KEY FK_D9191BD48D9F6D38');
        $this->addSql('ALTER TABLE sylius_payment ADD CONSTRAINT FK_D9191BD48D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id)');
        $this->addSql('ALTER TABLE sylius_promotion_order DROP FOREIGN KEY FK_BF9CF6FB8D9F6D38');
        $this->addSql('ALTER TABLE sylius_promotion_order ADD CONSTRAINT FK_BF9CF6FB8D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id)');
        $this->addSql('ALTER TABLE sylius_promotion_order_item DROP FOREIGN KEY FK_49838ED1E415FB15');
        $this->addSql('ALTER TABLE sylius_promotion_order_item ADD CONSTRAINT FK_49838ED1E415FB15 FOREIGN KEY (order_item_id) REFERENCES sylius_order_item (id)');
        $this->addSql('ALTER TABLE sylius_shipment DROP FOREIGN KEY FK_FD707B338D9F6D38');
        $this->addSql('ALTER TABLE sylius_shipment ADD CONSTRAINT FK_FD707B338D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id)');
    }
}
