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

final class Version20260618100001 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Add show_prices_including_tax column to sylius_channel table (PostgreSQL)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_channel ADD show_prices_including_tax BOOLEAN NOT NULL DEFAULT false');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_channel DROP COLUMN show_prices_including_tax');
    }
}
