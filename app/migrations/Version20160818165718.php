<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160818165718 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product DROP FOREIGN KEY FK_677B9B74732C6CC7');
        $this->addSql('ALTER TABLE sylius_product_archetype DROP FOREIGN KEY FK_A4001B52727ACA70');
        $this->addSql('ALTER TABLE sylius_product_archetype_attribute DROP FOREIGN KEY FK_97763342732C6CC7');
        $this->addSql('ALTER TABLE sylius_product_archetype_option DROP FOREIGN KEY FK_BCE763A7FE884EAC');
        $this->addSql('ALTER TABLE sylius_product_archetype_translation DROP FOREIGN KEY FK_E0BA36D2C2AC5D3');
        $this->addSql('DROP TABLE sylius_product_archetype');
        $this->addSql('DROP TABLE sylius_product_archetype_attribute');
        $this->addSql('DROP TABLE sylius_product_archetype_option');
        $this->addSql('DROP TABLE sylius_product_archetype_translation');
        $this->addSql('DROP INDEX IDX_677B9B74732C6CC7 ON sylius_product');
        $this->addSql('ALTER TABLE sylius_product DROP archetype_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_product_archetype (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_A4001B5277153098 (code), INDEX IDX_A4001B52727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_product_archetype_attribute (archetype_id INT NOT NULL, attribute_id INT NOT NULL, INDEX IDX_97763342732C6CC7 (archetype_id), INDEX IDX_97763342B6E62EFA (attribute_id), PRIMARY KEY(archetype_id, attribute_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_product_archetype_option (product_archetype_id INT NOT NULL, option_id INT NOT NULL, INDEX IDX_BCE763A7FE884EAC (product_archetype_id), INDEX IDX_BCE763A7A7C41D6F (option_id), PRIMARY KEY(product_archetype_id, option_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_product_archetype_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, locale VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX sylius_product_archetype_translation_uniq_trans (translatable_id, locale), INDEX IDX_E0BA36D2C2AC5D3 (translatable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_product_archetype ADD CONSTRAINT FK_A4001B52727ACA70 FOREIGN KEY (parent_id) REFERENCES sylius_product_archetype (id)');
        $this->addSql('ALTER TABLE sylius_product_archetype_attribute ADD CONSTRAINT FK_97763342732C6CC7 FOREIGN KEY (archetype_id) REFERENCES sylius_product_archetype (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_archetype_attribute ADD CONSTRAINT FK_97763342B6E62EFA FOREIGN KEY (attribute_id) REFERENCES sylius_product_attribute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_archetype_option ADD CONSTRAINT FK_BCE763A7A7C41D6F FOREIGN KEY (option_id) REFERENCES sylius_product_option (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_archetype_option ADD CONSTRAINT FK_BCE763A7FE884EAC FOREIGN KEY (product_archetype_id) REFERENCES sylius_product_archetype (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_archetype_translation ADD CONSTRAINT FK_E0BA36D2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES sylius_product_archetype (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product ADD archetype_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_product ADD CONSTRAINT FK_677B9B74732C6CC7 FOREIGN KEY (archetype_id) REFERENCES sylius_product_archetype (id)');
        $this->addSql('CREATE INDEX IDX_677B9B74732C6CC7 ON sylius_product (archetype_id)');
    }
}
