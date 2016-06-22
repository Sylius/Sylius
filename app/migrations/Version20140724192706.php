<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140724192706 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', "Migration can only be executed safely on 'mysql'.");

        $this->addSql('ALTER TABLE sylius_order ADD email VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE sylius_order o SET email = (SELECT email FROM sylius_user u WHERE u.id = o.user_id) WHERE user_id IS NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', "Migration can only be executed safely on 'mysql'.");

        $this->addSql('ALTER TABLE sylius_order DROP email');
    }
}
