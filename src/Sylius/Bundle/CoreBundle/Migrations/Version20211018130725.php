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

final class Version20211018130725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change json_array(deprecated) to json type';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_payment CHANGE details details JSON NOT NULL');
        $this->addSql('ALTER TABLE sylius_product_attribute_value CHANGE json_value json_value JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_gateway_config CHANGE config config JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_payment CHANGE details details LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_product_attribute_value CHANGE json_value json_value LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_gateway_config CHANGE config config LONGTEXT NOT NULL');
    }
}
