<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 *
 */
class Version20160926175601 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX code_idx ON sylius_taxon_image');
        $this->addSql('CREATE UNIQUE INDEX taxon_image_code_idx ON sylius_taxon_image (owner_id, code)');
        $this->addSql('DROP INDEX code_idx ON sylius_product_image');
        $this->addSql('CREATE UNIQUE INDEX product_image_code_idx ON sylius_product_image (owner_id, code)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX taxon_image_code_idx ON sylius_taxon_image');
        $this->addSql('CREATE UNIQUE INDEX code_idx ON sylius_taxon_image (owner_id, code)');
        $this->addSql('DROP INDEX product_image_code_idx ON sylius_product_image');
        $this->addSql('CREATE UNIQUE INDEX code_idx ON sylius_product_image (owner_id, code)');

    }
}
