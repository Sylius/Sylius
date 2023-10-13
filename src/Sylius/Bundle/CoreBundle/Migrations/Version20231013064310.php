<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractPostgreSQLMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231013064310 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Add all needed fields for the wholesale products functionality (PostgreSQL)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_variant ADD wholesale BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE sylius_order_item ADD is_wholesale BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE sylius_order_item_unit ADD quantity INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
//        ;
//;
//;
        $this->addSql('ALTER TABLE sylius_product_variant DROP wholesale');
        $this->addSql('ALTER TABLE sylius_order_item DROP is_wholesale');
        $this->addSql('ALTER TABLE sylius_order_item_unit DROP quantity');
    }
}
