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

final class Version20231003081106 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Changes column types from JSON to TEXT, as it is on MySQL database';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_attribute_value ALTER COLUMN json_value TYPE TEXT');
        $this->addSql('COMMENT ON COLUMN sylius_product_attribute_value.json_value IS \'(DC2Type:json)\'');

        $this->addSql('ALTER TABLE sylius_adjustment ALTER COLUMN details TYPE TEXT');
        $this->addSql('COMMENT ON COLUMN sylius_adjustment.details IS \'(DC2Type:json)\'');

        $this->addSql('ALTER TABLE sylius_payment ALTER COLUMN details TYPE TEXT');
        $this->addSql('COMMENT ON COLUMN sylius_payment.details IS \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_attribute_value ALTER COLUMN json_value TYPE JSON USING json(json_value)');
        $this->addSql('COMMENT ON COLUMN sylius_product_attribute_value.json_value IS NULL');

        $this->addSql('ALTER TABLE sylius_adjustment ALTER COLUMN details TYPE JSON USING json(details)');
        $this->addSql('COMMENT ON COLUMN sylius_adjustment.details IS NULL');

        $this->addSql('ALTER TABLE sylius_payment ALTER COLUMN details TYPE JSON USING json(details)');
        $this->addSql('COMMENT ON COLUMN sylius_payment.details IS NULL');
    }
}
