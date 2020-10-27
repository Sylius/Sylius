<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminApiBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201026144042 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_attribute ADD not_translatable_name VARCHAR(255) DEFAULT NULL, ADD translatable TINYINT(1) DEFAULT \'1\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_attribute DROP not_translatable_name, DROP translatable');
    }
}
