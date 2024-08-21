<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractPostgreSQLMigration;

final class Version20240614075446 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Remove encoder name and salt on user tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_admin_user DROP salt');
        $this->addSql('ALTER TABLE sylius_admin_user DROP encoder_name');
        $this->addSql('ALTER TABLE sylius_shop_user DROP salt');
        $this->addSql('ALTER TABLE sylius_shop_user DROP encoder_name');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_shop_user ADD salt VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE sylius_shop_user ADD encoder_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_admin_user ADD salt VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE sylius_admin_user ADD encoder_name VARCHAR(255) DEFAULT NULL');
    }
}
