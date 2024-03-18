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

final class Version20210830193340 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add information about applied catalog promotions on channel pricing';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_channel_pricing ADD applied_promotions JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_channel_pricing DROP applied_promotions');
    }
}
