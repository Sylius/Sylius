<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractPostgreSQLMigration;

final class Version20230927190918 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Add archived_at field on promotion tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_catalog_promotion ADD archived_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_promotion ADD archived_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_catalog_promotion DROP archived_at');
        $this->addSql('ALTER TABLE sylius_promotion DROP archived_at');
    }
}
