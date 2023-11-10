<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

final class Version20231013063513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add all needed fields for the single order item unit products functionality';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_variant ADD order_item_unit_generation_mode TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE sylius_order_item ADD is_single_unit TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE sylius_order_item_unit ADD quantity INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_variant DROP order_item_unit_generation_mode');
        $this->addSql('ALTER TABLE sylius_order_item DROP is_single_unit');
        $this->addSql('ALTER TABLE sylius_order_item_unit DROP quantity');
    }
}
