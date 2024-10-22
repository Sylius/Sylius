<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

final class Version20201130071338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make shipment adjustable';
    }

    public function up(Schema $schema): void
    {
        $adjustmentTable = $schema->getTable('sylius_adjustment');
        if ($adjustmentTable->hasColumn('shipment_id')) {
            return;
        }

        $this->addSql('ALTER TABLE sylius_adjustment ADD shipment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_adjustment ADD CONSTRAINT FK_ACA6E0F27BE036FC FOREIGN KEY (shipment_id) REFERENCES sylius_shipment (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_ACA6E0F27BE036FC ON sylius_adjustment (shipment_id)');
        $this->addSql('ALTER TABLE sylius_shipment ADD adjustments_total INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $adjustmentTable = $schema->getTable('sylius_adjustment');
        if (!$adjustmentTable->hasColumn('shipment_id')) {
            return;
        }

        $this->addSql('ALTER TABLE sylius_adjustment DROP FOREIGN KEY FK_ACA6E0F27BE036FC');
        $this->addSql('DROP INDEX IDX_ACA6E0F27BE036FC ON sylius_adjustment');
        $this->addSql('ALTER TABLE sylius_adjustment DROP shipment_id');
        $this->addSql('ALTER TABLE sylius_shipment DROP adjustments_total');
    }
}
