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

final class Version20220412144156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Sync MariaDB schema';
    }

    public function up(Schema $schema): void
    {
        if ($this->isMariaDb()) {
            $this->addSql('ALTER TABLE sylius_adjustment CHANGE details details LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
            $this->addSql('ALTER TABLE sylius_gateway_config CHANGE config config LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
            $this->addSql('ALTER TABLE sylius_payment CHANGE details details LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
            $this->addSql('ALTER TABLE sylius_product_attribute_value CHANGE json_value json_value LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        }
    }

    public function down(Schema $schema): void
    {
        if ($this->isMariaDb()) {
            $this->addSql('ALTER TABLE sylius_adjustment CHANGE details details LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
            $this->addSql('ALTER TABLE sylius_gateway_config CHANGE config config LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
            $this->addSql('ALTER TABLE sylius_payment CHANGE details details LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
            $this->addSql('ALTER TABLE sylius_product_attribute_value CHANGE json_value json_value LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_bin`');
        }
    }
}
