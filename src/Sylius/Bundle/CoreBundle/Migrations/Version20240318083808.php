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
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractPostgreSQLMigration;

final class Version20240318083808 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Change the json_value column type to jsonb in the sylius_product_attribute_value table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_attribute_value ALTER json_value TYPE JSONB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_attribute_value ALTER json_value TYPE JSON');
    }
}
