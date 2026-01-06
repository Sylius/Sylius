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

final class Version20241020131604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove locked, expires_at and credentials_expire_at columns from User model';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_admin_user DROP locked, DROP expires_at, DROP credentials_expire_at');
        $this->addSql('ALTER TABLE sylius_shop_user DROP locked, DROP expires_at, DROP credentials_expire_at');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_admin_user ADD locked TINYINT(1) NOT NULL, ADD expires_at DATETIME DEFAULT NULL, ADD credentials_expire_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_shop_user ADD locked TINYINT(1) NOT NULL, ADD expires_at DATETIME DEFAULT NULL, ADD credentials_expire_at DATETIME DEFAULT NULL');
    }
}
