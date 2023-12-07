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

final class Version20231207154902 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Change sylius_tax_rate amount column to be nullable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_tax_rate ALTER amount DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_tax_rate ALTER amount SET NOT NULL');
    }
}
