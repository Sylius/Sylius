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
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMySqlMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190109160409 extends AbstractMySqlMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_shop_user ADD encoder_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_admin_user ADD encoder_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_admin_user DROP encoder_name');
        $this->addSql('ALTER TABLE sylius_shop_user DROP encoder_name');
    }
}
