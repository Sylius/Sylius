<?php

declare(strict_types=1);

namespace Sylius\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170215143031 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_105A908989D9B62 ON sylius_product_translation');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_105A9084180C698989D9B62 ON sylius_product_translation (locale, slug)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_105A9084180C698989D9B62 ON sylius_product_translation');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_105A908989D9B62 ON sylius_product_translation (slug)');
    }
}
