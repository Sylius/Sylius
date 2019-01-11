<?php

declare(strict_types=1);

namespace Sylius\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170208103250 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE UNIQUE INDEX product_variant_channel_idx ON sylius_channel_pricing (product_variant_id, channel_id)');
        $this->addSql('CREATE UNIQUE INDEX product_taxon_idx ON sylius_product_taxon (product_id, taxon_id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX product_variant_channel_idx ON sylius_channel_pricing');
        $this->addSql('DROP INDEX product_taxon_idx ON sylius_product_taxon');
    }
}
