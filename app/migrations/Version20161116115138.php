<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161116115138 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sylius_product_taxon');
        $this->addSql('CREATE TABLE sylius_product_taxon (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, taxon_id INT NOT NULL, position INT NOT NULL, INDEX IDX_1539E9AF4584665A (product_id), INDEX IDX_1539E9AFDE13F470 (taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_product_taxon ADD CONSTRAINT FK_1539E9AF4584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_taxon ADD CONSTRAINT FK_1539E9AFDE13F470 FOREIGN KEY (taxon_id) REFERENCES sylius_taxon (id) ON DELETE CASCADE');

        $this->addSql('
            ALTER TABLE sylius_product_taxon DROP FOREIGN KEY FK_1539E9AF4584665A;
            ALTER TABLE sylius_product_taxon DROP FOREIGN KEY FK_1539E9AFDE13F470;
            DROP INDEX idx_1539e9af4584665a ON sylius_product_taxon;
            CREATE INDEX IDX_169C6CD94584665A ON sylius_product_taxon (product_id);
            DROP INDEX idx_1539e9afde13f470 ON sylius_product_taxon;
            CREATE INDEX IDX_169C6CD9DE13F470 ON sylius_product_taxon (taxon_id);
            ALTER TABLE sylius_product_taxon ADD CONSTRAINT FK_1539E9AF4584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE;
            ALTER TABLE sylius_product_taxon ADD CONSTRAINT FK_1539E9AFDE13F470 FOREIGN KEY (taxon_id) REFERENCES sylius_taxon (id) ON DELETE CASCADE;
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sylius_product_taxon');
        $this->addSql('CREATE TABLE sylius_product_taxon (product_id INT NOT NULL, taxon_id INT NOT NULL, INDEX IDX_169C6CD94584665A (product_id), INDEX IDX_169C6CD9DE13F470 (taxon_id), PRIMARY KEY(product_id, taxon_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_product_taxon ADD CONSTRAINT FK_169C6CD94584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_taxon ADD CONSTRAINT FK_169C6CD9DE13F470 FOREIGN KEY (taxon_id) REFERENCES sylius_taxon (id) ON DELETE CASCADE');
    }
}
