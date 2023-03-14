<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230314073130 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add lowest_price_before_discount column to sylius_channel_pricing table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_channel_pricing ADD lowest_price_before_discount INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_channel_pricing DROP lowest_price_before_discount');
    }
}
