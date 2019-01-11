<?php

declare(strict_types=1);

namespace Sylius\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170518123056 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_address_log_entries CHANGE loggedat logged_at DATETIME NOT NULL, CHANGE objectid object_id VARCHAR(64) DEFAULT NULL, CHANGE objectclass object_class VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_address_log_entries CHANGE logged_at loggedAt DATETIME NOT NULL, CHANGE object_id objectId VARCHAR(64) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE object_class objectClass VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}
