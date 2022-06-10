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

final class Version20220524072159 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update OAuth access_token and refresh_token length';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_user_oauth CHANGE access_token access_token VARCHAR(2048) DEFAULT NULL, CHANGE refresh_token refresh_token VARCHAR(2048) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_user_oauth CHANGE access_token access_token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE refresh_token refresh_token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}