<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210809121349 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Indexing of sylius_customer creation date';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX created_at_index ON sylius_customer (created_at DESC)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX created_at_index ON sylius_customer');
    }
}
