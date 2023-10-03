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

final class Version20230327121633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Allow longer access and refresh tokens for OAuth users';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_user_oauth CHANGE access_token access_token TEXT DEFAULT NULL, CHANGE refresh_token refresh_token TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_user_oauth CHANGE access_token access_token VARCHAR(255) DEFAULT NULL, CHANGE refresh_token refresh_token VARCHAR(255) DEFAULT NULL');
    }
}
