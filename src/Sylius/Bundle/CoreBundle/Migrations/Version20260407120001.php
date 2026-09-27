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

final class Version20260407120001 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Add track_usage column to sylius_promotion and sylius_promotion_coupon tables (PostgreSQL).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_promotion ADD track_usage BOOLEAN DEFAULT TRUE NOT NULL');
        $this->addSql('ALTER TABLE sylius_promotion_coupon ADD track_usage BOOLEAN DEFAULT TRUE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_promotion DROP track_usage');
        $this->addSql('ALTER TABLE sylius_promotion_coupon DROP track_usage');
    }
}
