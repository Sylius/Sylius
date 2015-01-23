<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150102231038 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE sylius_product_attribute_group (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_FAB222A58CDE5729 (type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_product_attribute ADD group_id INT');
        $this->addSql('ALTER TABLE sylius_product_attribute ADD CONSTRAINT FK_BFAF484AFE54D947 FOREIGN KEY (group_id) REFERENCES sylius_product_attribute_group (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_BFAF484AFE54D947 ON sylius_product_attribute (group_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE sylius_product_attribute DROP FOREIGN KEY FK_BFAF484AFE54D947');
        $this->addSql('DROP TABLE sylius_product_attribute_group');
        $this->addSql('DROP INDEX IDX_BFAF484AFE54D947 ON sylius_product_attribute');
        $this->addSql('ALTER TABLE sylius_product_attribute DROP group_id');
    }
}
