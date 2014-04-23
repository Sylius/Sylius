<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140409203042 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE sylius_product_option RENAME TO sylius_product_options");
        $this->addSql("ALTER TABLE sylius_option RENAME TO sylius_product_option");
        $this->addSql("ALTER TABLE sylius_option_value RENAME TO sylius_product_option_value");
        $this->addSql("ALTER TABLE sylius_variant_option_value RENAME TO sylius_product_variant_option_value");
        $this->addSql("ALTER TABLE sylius_property RENAME TO sylius_product_attribute");
        $this->addSql("ALTER TABLE sylius_product_property RENAME TO sylius_product_attribute_value");
        $this->addSql("ALTER TABLE sylius_prototype_property RENAME TO sylius_product_prototype_attribute");
        $this->addSql("ALTER TABLE sylius_prototype_option RENAME TO sylius_product_prototype_option");
        $this->addSql("ALTER TABLE sylius_variant RENAME TO sylius_product_variant");
        $this->addSql("ALTER TABLE sylius_variant_image RENAME TO sylius_product_variant_image");
        $this->addSql("ALTER TABLE sylius_prototype RENAME TO sylius_product_prototype");

        $this->addSql("ALTER TABLE sylius_product_option_value DROP FOREIGN KEY FK_E05AED9DA7C41D6F");
        $this->addSql("ALTER TABLE sylius_product_option_value ADD CONSTRAINT FK_F7FF7D4BA7C41D6F FOREIGN KEY (option_id) REFERENCES sylius_product_option (id) ON DELETE CASCADE");

        $this->addSql("ALTER TABLE sylius_product_attribute_value DROP FOREIGN KEY FK_8109D8F3549213EC");
        $this->addSql("ALTER TABLE sylius_product_attribute_value DROP FOREIGN KEY FK_8109D8F34584665A");
        $this->addSql("ALTER TABLE sylius_product_attribute_value CHANGE property_id attribute_id INT NOT NULL");
        $this->addSql("ALTER TABLE sylius_product_attribute_value ADD CONSTRAINT FK_8A053E54B6E62EFA FOREIGN KEY (attribute_id) REFERENCES sylius_product_attribute (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE sylius_product_attribute_value ADD CONSTRAINT FK_8A053E544584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE");

        $this->addSql("ALTER TABLE sylius_product_prototype_attribute DROP FOREIGN KEY FK_99041F2A549213EC");
        $this->addSql("ALTER TABLE sylius_product_prototype_attribute CHANGE property_id attribute_id INT NOT NULL");
        $this->addSql("ALTER TABLE sylius_product_prototype_attribute ADD CONSTRAINT FK_E0C47001B6E62EFA FOREIGN KEY (attribute_id) REFERENCES sylius_product_attribute (id)");
        $this->addSql("UPDATE sylius_product_variant SET pricing_calculator = 'standard', pricing_configuration = 'a:0:{}'");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
    }
}
