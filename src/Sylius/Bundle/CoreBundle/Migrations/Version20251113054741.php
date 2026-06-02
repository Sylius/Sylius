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
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractPostgreSQLMigration;

final class Version20251113054741 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Adds minimum and maximum delivery time (in days) fields to the sylius_shipping_method table (PostgreSQL).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_shipping_method ADD min_delivery_time_days INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_shipping_method ADD max_delivery_time_days INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_shipping_method DROP min_delivery_time_days');
        $this->addSql('ALTER TABLE sylius_shipping_method DROP max_delivery_time_days');
    }
}
