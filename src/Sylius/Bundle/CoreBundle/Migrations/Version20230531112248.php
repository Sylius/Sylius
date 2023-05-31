<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230531112248 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make total in OrderItem float so taxes are rounded after they are summed for all items';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_order_item CHANGE total total DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_order_item CHANGE total total INT NOT NULL');
    }
}
