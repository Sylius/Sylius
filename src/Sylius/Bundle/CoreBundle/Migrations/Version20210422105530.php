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

final class Version20210422105530 extends AbstractMySqlMigration
{
    public function getDescription(): string
    {
        return 'Add version field to order item';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_order_item ADD version INT DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_order_item DROP version');
    }
}
