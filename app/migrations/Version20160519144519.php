<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160519144519 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX fulltext_search_idx ON sylius_search_index');
        $this->addSql('CREATE INDEX fulltext_search_idx ON sylius_search_index (item_id)');
        $this->addSql('DROP INDEX IDX_A29B523F9038C4 ON sylius_product_variant');
        $this->addSql('ALTER TABLE sylius_product_variant CHANGE sku code VARCHAR(255) NUL NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A29B52377153098 ON sylius_product_variant (code)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_A29B52377153098 ON sylius_product_variant');
        $this->addSql('ALTER TABLE sylius_product_variant CHANGE code sku VARCHAR(255) NUL NULL');
        $this->addSql('CREATE INDEX IDX_A29B523F9038C4 ON sylius_product_variant (sku)');
        $this->addSql('DROP INDEX fulltext_search_idx ON sylius_search_index');
        $this->addSql('CREATE FULLTEXT INDEX fulltext_search_idx ON sylius_search_index (value)');
    }
}
