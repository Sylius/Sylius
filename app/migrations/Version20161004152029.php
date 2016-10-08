<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161004152029 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product DROP FOREIGN KEY FK_677B9B749E2D1A41');
        $this->addSql('ALTER TABLE sylius_shipping_method DROP FOREIGN KEY FK_5FB0EE1112469DE2');
        $this->addSql('DROP TABLE sylius_shipping_category');
        $this->addSql('DROP INDEX IDX_677B9B749E2D1A41 ON sylius_product');
        $this->addSql('ALTER TABLE sylius_product DROP shipping_category_id');
        $this->addSql('DROP INDEX IDX_5FB0EE1112469DE2 ON sylius_shipping_method');
        $this->addSql('ALTER TABLE sylius_shipping_method DROP category_id, DROP category_requirement');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_shipping_category (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, description LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_B1D6465277153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_product ADD shipping_category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_product ADD CONSTRAINT FK_677B9B749E2D1A41 FOREIGN KEY (shipping_category_id) REFERENCES sylius_shipping_category (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_677B9B749E2D1A41 ON sylius_product (shipping_category_id)');
        $this->addSql('ALTER TABLE sylius_shipping_method ADD category_id INT DEFAULT NULL, ADD category_requirement INT NOT NULL');
        $this->addSql('ALTER TABLE sylius_shipping_method ADD CONSTRAINT FK_5FB0EE1112469DE2 FOREIGN KEY (category_id) REFERENCES sylius_shipping_category (id)');
        $this->addSql('CREATE INDEX IDX_5FB0EE1112469DE2 ON sylius_shipping_method (category_id)');
    }
}
