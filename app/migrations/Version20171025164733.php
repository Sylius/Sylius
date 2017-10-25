<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171025164733 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_product_attribute_select_option (id INT AUTO_INCREMENT NOT NULL, attribute_id INT NOT NULL, INDEX IDX_BBD712F3B6E62EFA (attribute_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_product_attribute_select_option_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_58A17C0F2C2AC5D3 (translatable_id), UNIQUE INDEX sylius_product_attribute_select_option_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_product_attribute_value_attribute_select_option (productattributevalue_id INT NOT NULL, productattributeselectoption_id INT NOT NULL, INDEX IDX_E80652A21B91253 (productattributevalue_id), INDEX IDX_E80652A5881538D (productattributeselectoption_id), PRIMARY KEY(productattributevalue_id, productattributeselectoption_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_product_attribute_select_option ADD CONSTRAINT FK_BBD712F3B6E62EFA FOREIGN KEY (attribute_id) REFERENCES sylius_product_attribute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_attribute_select_option_translation ADD CONSTRAINT FK_58A17C0F2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES sylius_product_attribute_select_option (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_attribute_value_attribute_select_option ADD CONSTRAINT FK_E80652A21B91253 FOREIGN KEY (productattributevalue_id) REFERENCES sylius_product_attribute_value (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_attribute_value_attribute_select_option ADD CONSTRAINT FK_E80652A5881538D FOREIGN KEY (productattributeselectoption_id) REFERENCES sylius_product_attribute_select_option (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product_attribute_select_option_translation DROP FOREIGN KEY FK_58A17C0F2C2AC5D3');
        $this->addSql('ALTER TABLE sylius_product_attribute_value_attribute_select_option DROP FOREIGN KEY FK_E80652A5881538D');
        $this->addSql('DROP TABLE sylius_product_attribute_select_option');
        $this->addSql('DROP TABLE sylius_product_attribute_select_option_translation');
        $this->addSql('DROP TABLE sylius_product_attribute_value_attribute_select_option');
    }
}
