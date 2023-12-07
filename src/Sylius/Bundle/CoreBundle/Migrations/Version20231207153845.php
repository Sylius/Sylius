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

final class Version20231207153845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change sylius_tax_rate amount column to be nullable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_tax_rate CHANGE amount amount NUMERIC(10, 5) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_tax_rate CHANGE amount amount NUMERIC(10, 5) NOT NULL');
    }
}
