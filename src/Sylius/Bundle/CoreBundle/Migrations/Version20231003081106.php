<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractPostgreSQLMigration;

final class Version20231003081106 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Changes sylius_product_attribute_value json_value type from JSON to TEXT, as it is on MySQL database';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_attribute_value ALTER COLUMN json_value TYPE TEXT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_attribute_value ALTER COLUMN json_value TYPE JSON');
    }
}
