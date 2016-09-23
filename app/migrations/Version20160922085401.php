<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160922085401 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_product_image (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, code VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, INDEX IDX_88C64B2D7E3C61F9 (owner_id), UNIQUE INDEX code_idx (owner_id, code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_product_image ADD CONSTRAINT FK_88C64B2D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES sylius_product (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE sylius_product_variant_image');
        $this->addSql('ALTER TABLE sylius_taxon_image DROP FOREIGN KEY FK_DBE52B28DE13F470');
        $this->addSql('DROP INDEX IDX_DBE52B28DE13F470 ON sylius_taxon_image');
        $this->addSql('DROP INDEX code_idx ON sylius_taxon_image');
        $this->addSql('ALTER TABLE sylius_taxon_image DROP created_at, DROP updated_at, CHANGE taxon_id owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE sylius_taxon_image ADD CONSTRAINT FK_DBE52B287E3C61F9 FOREIGN KEY (owner_id) REFERENCES sylius_taxon (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_DBE52B287E3C61F9 ON sylius_taxon_image (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX code_idx ON sylius_taxon_image (owner_id, code)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_product_variant_image (id INT AUTO_INCREMENT NOT NULL, variant_id INT DEFAULT NULL, path VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_C6B77D5D3B69A9AF (variant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_product_variant_image ADD CONSTRAINT FK_C6B77D5D3B69A9AF FOREIGN KEY (variant_id) REFERENCES sylius_product_variant (id)');
        $this->addSql('DROP TABLE sylius_product_image');
        $this->addSql('ALTER TABLE sylius_taxon_image DROP FOREIGN KEY FK_DBE52B287E3C61F9');
        $this->addSql('DROP INDEX IDX_DBE52B287E3C61F9 ON sylius_taxon_image');
        $this->addSql('DROP INDEX code_idx ON sylius_taxon_image');
        $this->addSql('ALTER TABLE sylius_taxon_image ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, CHANGE owner_id taxon_id INT NOT NULL');
        $this->addSql('ALTER TABLE sylius_taxon_image ADD CONSTRAINT FK_DBE52B28DE13F470 FOREIGN KEY (taxon_id) REFERENCES sylius_taxon (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_DBE52B28DE13F470 ON sylius_taxon_image (taxon_id)');
        $this->addSql('CREATE UNIQUE INDEX code_idx ON sylius_taxon_image (taxon_id, code)');
    }
}
