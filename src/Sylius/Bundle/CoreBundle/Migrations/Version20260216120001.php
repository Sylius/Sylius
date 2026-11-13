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

final class Version20260216120001 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Add version column to sylius_promotion and sylius_promotion_coupon tables for optimistic locking (PostgreSQL).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_promotion ADD version INT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE sylius_promotion_coupon ADD version INT DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_promotion DROP version');
        $this->addSql('ALTER TABLE sylius_promotion_coupon DROP version');
    }
}
