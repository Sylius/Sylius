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

final class Version20241020131614 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Remove locked, expires_at and credentials_expire_at columns from User model';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_admin_user DROP locked');
        $this->addSql('ALTER TABLE sylius_admin_user DROP expires_at');
        $this->addSql('ALTER TABLE sylius_admin_user DROP credentials_expire_at');
        $this->addSql('ALTER TABLE sylius_shop_user DROP locked');
        $this->addSql('ALTER TABLE sylius_shop_user DROP expires_at');
        $this->addSql('ALTER TABLE sylius_shop_user DROP credentials_expire_at');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_admin_user ADD locked BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE sylius_admin_user ADD expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_admin_user ADD credentials_expire_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_shop_user ADD locked BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE sylius_shop_user ADD expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_shop_user ADD credentials_expire_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }
}
