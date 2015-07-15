<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140430223207 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        // Create new `order_id` column to `sylius_payment` table
        $this->addSql("ALTER TABLE sylius_payment ADD order_id INT NOT NULL");

        // Fill order ids
        $this->addSql("UPDATE sylius_payment p
            JOIN (SELECT id, payment_id FROM sylius_order WHERE sylius_order.payment_id IS NOT NULL) o ON o.payment_id = p.id
            SET p.order_id = o.id");

        // Delete payment that doesn't link with any order
        $this->addSql("DELETE FROM `sylius_payment` WHERE order_id = 0");

        // Add foreign key and constraint
        $this->addSql("ALTER TABLE sylius_payment ADD CONSTRAINT FK_D9191BD48D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id)");
        $this->addSql("CREATE INDEX IDX_D9191BD48D9F6D38 ON sylius_payment (order_id)");

        // Drop `payment_id` from `sylius_order` table
        $this->addSql("ALTER TABLE sylius_order DROP FOREIGN KEY FK_6196A1F94C3A3BB");
        $this->addSql("DROP INDEX IDX_6196A1F94C3A3BB ON sylius_order");
        $this->addSql("ALTER TABLE sylius_order DROP payment_id");
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE sylius_order ADD payment_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE sylius_order ADD CONSTRAINT FK_6196A1F94C3A3BB FOREIGN KEY (payment_id) REFERENCES sylius_payment (id)");
        $this->addSql("CREATE INDEX IDX_6196A1F94C3A3BB ON sylius_order (payment_id)");

        // Fill payment ids
        $this->addSql("UPDATE sylius_order o
            JOIN sylius_payment p ON o.id = p.order_id
            SET o.payment_id = p.id");

        $this->addSql("ALTER TABLE sylius_payment DROP FOREIGN KEY FK_D9191BD48D9F6D38");
        $this->addSql("DROP INDEX IDX_D9191BD48D9F6D38 ON sylius_payment");
        $this->addSql("ALTER TABLE sylius_payment DROP order_id");
    }
}
