<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160212163549 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_adjustment ADD is_refund TINYINT(1) NOT NULL');
        $this->addSql('CREATE INDEX type_refund_idx ON sylius_adjustment (type, is_refund)');
        $this->addSql('CREATE INDEX refund_idx ON sylius_adjustment (is_refund)');
        $this->addSql('ALTER TABLE sylius_order ADD items_refund_total INT NOT NULL, ADD refund_adjustments_total INT NOT NULL, ADD refund_total INT NOT NULL');
        $this->addSql('ALTER TABLE sylius_order_item ADD units_refund_total INT NOT NULL, ADD refund_adjustments_total INT NOT NULL, ADD refund_total INT NOT NULL');
        $this->addSql('ALTER TABLE sylius_order_item_unit ADD refund_adjustments_total INT NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX type_refund_idx ON sylius_adjustment');
        $this->addSql('DROP INDEX refund_idx ON sylius_adjustment');
        $this->addSql('ALTER TABLE sylius_adjustment DROP is_refund');
        $this->addSql('ALTER TABLE sylius_order DROP items_refund_total, DROP refund_adjustments_total, DROP refund_total');
        $this->addSql('ALTER TABLE sylius_order_item DROP units_refund_total, DROP refund_adjustments_total, DROP refund_total');
        $this->addSql('ALTER TABLE sylius_order_item_unit DROP refund_adjustments_total');
    }
}
