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

namespace Sylius\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

class Version20170518123056 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_address_log_entries CHANGE loggedat logged_at DATETIME NOT NULL, CHANGE objectid object_id VARCHAR(64) DEFAULT NULL, CHANGE objectclass object_class VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_address_log_entries CHANGE logged_at loggedAt DATETIME NOT NULL, CHANGE object_id objectId VARCHAR(64) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE object_class objectClass VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}
