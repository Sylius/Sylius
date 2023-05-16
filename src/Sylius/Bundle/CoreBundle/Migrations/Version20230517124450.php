<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230517124450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make prices in Adjustment and OrderItemUnit float so prices are calculated correctly when one order item unit costs less than one cent';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_adjustment CHANGE amount amount DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE sylius_order_item CHANGE unit_price unit_price DOUBLE PRECISION NOT NULL, CHANGE units_total units_total DOUBLE PRECISION NOT NULL, CHANGE adjustments_total adjustments_total DOUBLE PRECISION NOT NULL, CHANGE original_unit_price original_unit_price DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_order_item_unit CHANGE adjustments_total adjustments_total DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_adjustment CHANGE amount amount INT NOT NULL');
        $this->addSql('ALTER TABLE sylius_order_item CHANGE unit_price unit_price INT NOT NULL, CHANGE original_unit_price original_unit_price INT DEFAULT NULL, CHANGE units_total units_total INT NOT NULL, CHANGE adjustments_total adjustments_total INT NOT NULL');
        $this->addSql('ALTER TABLE sylius_order_item_unit CHANGE adjustments_total adjustments_total INT NOT NULL');
    }
}
