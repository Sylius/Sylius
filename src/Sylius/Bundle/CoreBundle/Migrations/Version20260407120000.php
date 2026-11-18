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

final class Version20260407120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add track_usage column to sylius_promotion and sylius_promotion_coupon tables.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_promotion ADD track_usage TINYINT(1) DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE sylius_promotion_coupon ADD track_usage TINYINT(1) DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_promotion DROP track_usage');
        $this->addSql('ALTER TABLE sylius_promotion_coupon DROP track_usage');
    }
}
