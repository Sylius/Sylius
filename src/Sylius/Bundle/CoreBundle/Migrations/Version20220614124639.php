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

final class Version20220614124639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'null values in minimum price field changed to default';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE sylius_channel_pricing SET minimum_price = 0 WHERE minimum_price IS NULL');
    }

    public function down(Schema $schema): void
    {
    }
}
