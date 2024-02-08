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

class Version20161220092422 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE sylius_address_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(255) NOT NULL, loggedAt DATETIME NOT NULL, objectId VARCHAR(64) DEFAULT NULL, objectClass VARCHAR(255) NOT NULL, version INT NOT NULL, data LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE sylius_address_log_entries');
    }
}
