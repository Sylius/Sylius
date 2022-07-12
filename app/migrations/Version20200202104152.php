<?php

declare(strict_types=1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200202104152 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf(!is_a($this->connection->getDatabasePlatform(), \Doctrine\DBAL\Platforms\MySqlPlatform::class, true), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_shipment ADD shipped_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(!is_a($this->connection->getDatabasePlatform(), \Doctrine\DBAL\Platforms\MySqlPlatform::class, true), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_shipment DROP shipped_at');
    }
}
