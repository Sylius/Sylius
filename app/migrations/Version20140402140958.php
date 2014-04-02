<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration from 0.9.0 to 0.10.x.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Version20140402140958 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE sylius_option_value DROP FOREIGN KEY FK_E05AED9DA7C41D6F");
        $this->addSql("ALTER TABLE sylius_product_option DROP FOREIGN KEY FK_E4C0EBEFA7C41D6F");
        $this->addSql("ALTER TABLE sylius_prototype_option DROP FOREIGN KEY FK_B458391BA7C41D6F");
        $this->addSql("ALTER TABLE sylius_variant_option_value DROP FOREIGN KEY FK_C2666DC9D957CA06");
        $this->addSql("ALTER TABLE sylius_product_property DROP FOREIGN KEY FK_8109D8F3549213EC");
        $this->addSql("ALTER TABLE sylius_prototype_property DROP FOREIGN KEY FK_99041F2A549213EC");
        $this->addSql("ALTER TABLE sylius_prototype_option DROP FOREIGN KEY FK_B458391B25998077");
        $this->addSql("ALTER TABLE sylius_prototype_property DROP FOREIGN KEY FK_99041F2A25998077");
        $this->addSql("ALTER TABLE sylius_inventory_unit DROP FOREIGN KEY FK_4A276986FBE8234");
        $this->addSql("ALTER TABLE sylius_order_item DROP FOREIGN KEY FK_77B587ED3B69A9AF");
        $this->addSql("ALTER TABLE sylius_variant_image DROP FOREIGN KEY FK_A910AF2C3B69A9AF");
        $this->addSql("ALTER TABLE sylius_variant_option_value DROP FOREIGN KEY FK_C2666DC93B69A9AF");
        $this->addSql("CREATE TABLE sylius_product_attribute_value (id INT AUTO_INCREMENT NOT NULL, subject_id INT NOT NULL, attribute_id INT NOT NULL, value LONGTEXT NOT NULL, INDEX IDX_8A053E5423EDC87 (subject_id), INDEX IDX_8A053E54B6E62EFA (attribute_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sylius_product_prototype (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sylius_product_prototype_attribute (prototype_id INT NOT NULL, attribute_id INT NOT NULL, INDEX IDX_E0C4700125998077 (prototype_id), INDEX IDX_E0C47001B6E62EFA (attribute_id), PRIMARY KEY(prototype_id, attribute_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sylius_product_prototype_option (prototype_id INT NOT NULL, option_id INT NOT NULL, INDEX IDX_1AD7AAC525998077 (prototype_id), INDEX IDX_1AD7AAC5A7C41D6F (option_id), PRIMARY KEY(prototype_id, option_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sylius_product_option_value (id INT AUTO_INCREMENT NOT NULL, option_id INT NOT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_F7FF7D4BA7C41D6F (option_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sylius_product_attribute (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, presentation VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, configuration LONGTEXT NOT NULL COMMENT '(DC2Type:array)', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sylius_product_variant (id INT AUTO_INCREMENT NOT NULL, object_id INT NOT NULL, is_master TINYINT(1) NOT NULL, presentation VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, available_on DATETIME NOT NULL, sku VARCHAR(255) DEFAULT NULL, price INT NOT NULL, on_hold INT NOT NULL, on_hand INT NOT NULL, available_on_demand TINYINT(1) NOT NULL, width DOUBLE PRECISION DEFAULT NULL, height DOUBLE PRECISION DEFAULT NULL, depth DOUBLE PRECISION DEFAULT NULL, weight DOUBLE PRECISION DEFAULT NULL, INDEX IDX_A29B523232D562B (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE product_variant_option_value (variant_id INT NOT NULL, option_value_id INT NOT NULL, INDEX IDX_82F964363B69A9AF (variant_id), INDEX IDX_82F96436D957CA06 (option_value_id), PRIMARY KEY(variant_id, option_value_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sylius_product_options (product_id INT NOT NULL, option_id INT NOT NULL, INDEX IDX_2B5FF0094584665A (product_id), INDEX IDX_2B5FF009A7C41D6F (option_id), PRIMARY KEY(product_id, option_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sylius_product_variant_image (id INT AUTO_INCREMENT NOT NULL, variant_id INT DEFAULT NULL, path VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_C6B77D5D3B69A9AF (variant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE sylius_product_attribute_value ADD CONSTRAINT FK_8A053E5423EDC87 FOREIGN KEY (subject_id) REFERENCES sylius_product (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE sylius_product_attribute_value ADD CONSTRAINT FK_8A053E54B6E62EFA FOREIGN KEY (attribute_id) REFERENCES sylius_product_attribute (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE sylius_product_prototype_attribute ADD CONSTRAINT FK_E0C4700125998077 FOREIGN KEY (prototype_id) REFERENCES sylius_product_prototype (id)");
        $this->addSql("ALTER TABLE sylius_product_prototype_attribute ADD CONSTRAINT FK_E0C47001B6E62EFA FOREIGN KEY (attribute_id) REFERENCES sylius_product_attribute (id)");
        $this->addSql("ALTER TABLE sylius_product_prototype_option ADD CONSTRAINT FK_1AD7AAC525998077 FOREIGN KEY (prototype_id) REFERENCES sylius_product_prototype (id)");
        $this->addSql("ALTER TABLE sylius_product_prototype_option ADD CONSTRAINT FK_1AD7AAC5A7C41D6F FOREIGN KEY (option_id) REFERENCES sylius_product_option (id)");
        $this->addSql("ALTER TABLE sylius_product_option_value ADD CONSTRAINT FK_F7FF7D4BA7C41D6F FOREIGN KEY (option_id) REFERENCES sylius_product_option (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE sylius_product_variant ADD CONSTRAINT FK_A29B523232D562B FOREIGN KEY (object_id) REFERENCES sylius_product (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE product_variant_option_value ADD CONSTRAINT FK_82F964363B69A9AF FOREIGN KEY (variant_id) REFERENCES sylius_product_variant (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE product_variant_option_value ADD CONSTRAINT FK_82F96436D957CA06 FOREIGN KEY (option_value_id) REFERENCES sylius_product_option_value (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE sylius_product_options ADD CONSTRAINT FK_2B5FF0094584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE sylius_product_options ADD CONSTRAINT FK_2B5FF009A7C41D6F FOREIGN KEY (option_id) REFERENCES sylius_product_option (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE sylius_product_variant_image ADD CONSTRAINT FK_C6B77D5D3B69A9AF FOREIGN KEY (variant_id) REFERENCES sylius_product_variant (id)");
        $this->addSql("DROP TABLE sylius_option");
        $this->addSql("DROP TABLE sylius_option_value");
        $this->addSql("DROP TABLE sylius_product_property");
        $this->addSql("DROP TABLE sylius_property");
        $this->addSql("DROP TABLE sylius_prototype");
        $this->addSql("DROP TABLE sylius_prototype_option");
        $this->addSql("DROP TABLE sylius_prototype_property");
        $this->addSql("DROP TABLE sylius_variant");
        $this->addSql("DROP TABLE sylius_variant_image");
        $this->addSql("DROP TABLE sylius_variant_option_value");
        $this->addSql("DROP INDEX IDX_E4C0EBEF4584665A ON sylius_product_option");
        $this->addSql("DROP INDEX IDX_E4C0EBEFA7C41D6F ON sylius_product_option");
        $this->addSql("ALTER TABLE sylius_product_option DROP PRIMARY KEY");
        $this->addSql("ALTER TABLE sylius_product_option ADD id INT AUTO_INCREMENT NOT NULL, ADD name VARCHAR(255) NOT NULL, ADD presentation VARCHAR(255) NOT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, DROP product_id, DROP option_id");
        $this->addSql("ALTER TABLE sylius_product_option ADD PRIMARY KEY (id)");
        $this->addSql("ALTER TABLE sylius_inventory_unit DROP FOREIGN KEY FK_4A276986FBE8234");
        $this->addSql("ALTER TABLE sylius_inventory_unit ADD CONSTRAINT FK_4A276986FBE8234 FOREIGN KEY (stockable_id) REFERENCES sylius_product_variant (id)");
        $this->addSql("ALTER TABLE sylius_order_item DROP FOREIGN KEY FK_77B587ED3B69A9AF");
        $this->addSql("ALTER TABLE sylius_order_item ADD CONSTRAINT FK_77B587ED3B69A9AF FOREIGN KEY (variant_id) REFERENCES sylius_product_variant (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE sylius_product_prototype_attribute DROP FOREIGN KEY FK_E0C4700125998077");
        $this->addSql("ALTER TABLE sylius_product_prototype_option DROP FOREIGN KEY FK_1AD7AAC525998077");
        $this->addSql("ALTER TABLE product_variant_option_value DROP FOREIGN KEY FK_82F96436D957CA06");
        $this->addSql("ALTER TABLE sylius_product_attribute_value DROP FOREIGN KEY FK_8A053E54B6E62EFA");
        $this->addSql("ALTER TABLE sylius_product_prototype_attribute DROP FOREIGN KEY FK_E0C47001B6E62EFA");
        $this->addSql("ALTER TABLE product_variant_option_value DROP FOREIGN KEY FK_82F964363B69A9AF");
        $this->addSql("ALTER TABLE sylius_inventory_unit DROP FOREIGN KEY FK_4A276986FBE8234");
        $this->addSql("ALTER TABLE sylius_product_variant_image DROP FOREIGN KEY FK_C6B77D5D3B69A9AF");
        $this->addSql("ALTER TABLE sylius_order_item DROP FOREIGN KEY FK_77B587ED3B69A9AF");
        $this->addSql("CREATE TABLE sylius_option (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, presentation VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sylius_option_value (id INT AUTO_INCREMENT NOT NULL, option_id INT NOT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_E05AED9DA7C41D6F (option_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sylius_product_property (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, property_id INT NOT NULL, value LONGTEXT NOT NULL, INDEX IDX_8109D8F34584665A (product_id), INDEX IDX_8109D8F3549213EC (property_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sylius_property (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, presentation VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, configuration LONGTEXT NOT NULL COMMENT '(DC2Type:array)', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sylius_prototype (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sylius_prototype_option (prototype_id INT NOT NULL, option_id INT NOT NULL, INDEX IDX_B458391B25998077 (prototype_id), INDEX IDX_B458391BA7C41D6F (option_id), PRIMARY KEY(prototype_id, option_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sylius_prototype_property (prototype_id INT NOT NULL, property_id INT NOT NULL, INDEX IDX_99041F2A25998077 (prototype_id), INDEX IDX_99041F2A549213EC (property_id), PRIMARY KEY(prototype_id, property_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sylius_variant (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, is_master TINYINT(1) NOT NULL, presentation VARCHAR(255) DEFAULT NULL, available_on DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, sku VARCHAR(255) DEFAULT NULL, price INT NOT NULL, on_hold INT NOT NULL, on_hand INT NOT NULL, available_on_demand TINYINT(1) NOT NULL, width DOUBLE PRECISION DEFAULT NULL, height DOUBLE PRECISION DEFAULT NULL, depth DOUBLE PRECISION DEFAULT NULL, weight DOUBLE PRECISION DEFAULT NULL, INDEX IDX_457220744584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sylius_variant_image (id INT AUTO_INCREMENT NOT NULL, variant_id INT DEFAULT NULL, path VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_A910AF2C3B69A9AF (variant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sylius_variant_option_value (variant_id INT NOT NULL, option_value_id INT NOT NULL, INDEX IDX_C2666DC93B69A9AF (variant_id), INDEX IDX_C2666DC9D957CA06 (option_value_id), PRIMARY KEY(variant_id, option_value_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE sylius_option_value ADD CONSTRAINT FK_E05AED9DA7C41D6F FOREIGN KEY (option_id) REFERENCES sylius_option (id)");
        $this->addSql("ALTER TABLE sylius_product_property ADD CONSTRAINT FK_8109D8F34584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id)");
        $this->addSql("ALTER TABLE sylius_product_property ADD CONSTRAINT FK_8109D8F3549213EC FOREIGN KEY (property_id) REFERENCES sylius_property (id)");
        $this->addSql("ALTER TABLE sylius_prototype_option ADD CONSTRAINT FK_B458391B25998077 FOREIGN KEY (prototype_id) REFERENCES sylius_prototype (id)");
        $this->addSql("ALTER TABLE sylius_prototype_option ADD CONSTRAINT FK_B458391BA7C41D6F FOREIGN KEY (option_id) REFERENCES sylius_option (id)");
        $this->addSql("ALTER TABLE sylius_prototype_property ADD CONSTRAINT FK_99041F2A25998077 FOREIGN KEY (prototype_id) REFERENCES sylius_prototype (id)");
        $this->addSql("ALTER TABLE sylius_prototype_property ADD CONSTRAINT FK_99041F2A549213EC FOREIGN KEY (property_id) REFERENCES sylius_property (id)");
        $this->addSql("ALTER TABLE sylius_variant ADD CONSTRAINT FK_457220744584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE sylius_variant_image ADD CONSTRAINT FK_A910AF2C3B69A9AF FOREIGN KEY (variant_id) REFERENCES sylius_variant (id)");
        $this->addSql("ALTER TABLE sylius_variant_option_value ADD CONSTRAINT FK_C2666DC93B69A9AF FOREIGN KEY (variant_id) REFERENCES sylius_variant (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE sylius_variant_option_value ADD CONSTRAINT FK_C2666DC9D957CA06 FOREIGN KEY (option_value_id) REFERENCES sylius_option_value (id) ON DELETE CASCADE");
        $this->addSql("DROP TABLE sylius_product_attribute_value");
        $this->addSql("DROP TABLE sylius_product_prototype");
        $this->addSql("DROP TABLE sylius_product_prototype_attribute");
        $this->addSql("DROP TABLE sylius_product_prototype_option");
        $this->addSql("DROP TABLE sylius_product_option_value");
        $this->addSql("DROP TABLE sylius_product_attribute");
        $this->addSql("DROP TABLE sylius_product_variant");
        $this->addSql("DROP TABLE product_variant_option_value");
        $this->addSql("DROP TABLE sylius_product_options");
        $this->addSql("DROP TABLE sylius_product_variant_image");
        $this->addSql("ALTER TABLE sylius_inventory_unit DROP FOREIGN KEY FK_4A276986FBE8234");
        $this->addSql("ALTER TABLE sylius_inventory_unit ADD CONSTRAINT FK_4A276986FBE8234 FOREIGN KEY (stockable_id) REFERENCES sylius_variant (id)");
        $this->addSql("ALTER TABLE sylius_order_item DROP FOREIGN KEY FK_77B587ED3B69A9AF");
        $this->addSql("ALTER TABLE sylius_order_item ADD CONSTRAINT FK_77B587ED3B69A9AF FOREIGN KEY (variant_id) REFERENCES sylius_variant (id)");
        $this->addSql("ALTER TABLE sylius_product_option DROP PRIMARY KEY");
        $this->addSql("ALTER TABLE sylius_product_option ADD product_id INT NOT NULL, ADD option_id INT NOT NULL, DROP id, DROP name, DROP presentation, DROP created_at, DROP updated_at");
        $this->addSql("ALTER TABLE sylius_product_option ADD CONSTRAINT FK_E4C0EBEF4584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE sylius_product_option ADD CONSTRAINT FK_E4C0EBEFA7C41D6F FOREIGN KEY (option_id) REFERENCES sylius_option (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_E4C0EBEF4584665A ON sylius_product_option (product_id)");
        $this->addSql("CREATE INDEX IDX_E4C0EBEFA7C41D6F ON sylius_product_option (option_id)");
        $this->addSql("ALTER TABLE sylius_product_option ADD PRIMARY KEY (product_id, option_id)");
    }
}
