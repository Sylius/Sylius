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

        $this->addSql("ALTER TABLE sylius_option_value RENAME TO sylius_product_option_value");
        $this->addSql("ALTER TABLE sylius_product_option RENAME TO sylius_product_product_option");
        $this->addSql("ALTER TABLE sylius_variant_option_value RENAME TO sylius_product_variant_option_value");
        $this->addSql("ALTER TABLE sylius_property RENAME TO sylius_product_attribute");
        $this->addSql("ALTER TABLE sylius_product_property RENAME TO sylius_product_attribute_value");
        $this->addSql("ALTER TABLE sylius_prototype_property RENAME TO sylius_product_prototype_attribute");
        $this->addSql("ALTER TABLE sylius_prototype_option RENAME TO sylius_product_prototype_option");
        $this->addSql("ALTER TABLE sylius_variant RENAME TO sylius_product_variant");
        $this->addSql("ALTER TABLE sylius_variant_image RENAME TO sylius_product_variant_image");

        $this->addSql("ALTER TABLE sylius_product_attribute_value CHANGE property_id attribute_id INT");

        $this->addSql("ALTER TABLE sylius_product_attribute_value ADD CONSTRAINT FK_8A053E54486F3B9F FOREIGN KEY (0_id) REFERENCES sylius_product (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE sylius_product_attribute_value ADD CONSTRAINT FK_8A053E54B6E62EFA FOREIGN KEY (attribute_id) REFERENCES sylius_product_attribute (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE sylius_product_prototype_attribute ADD CONSTRAINT FK_E0C4700125998077 FOREIGN KEY (prototype_id) REFERENCES sylius_product_prototype (id)");
        $this->addSql("ALTER TABLE sylius_product_prototype_attribute ADD CONSTRAINT FK_E0C47001B6E62EFA FOREIGN KEY (attribute_id) REFERENCES sylius_product_attribute (id)");
        $this->addSql("ALTER TABLE sylius_product_prototype_option ADD CONSTRAINT FK_1AD7AAC525998077 FOREIGN KEY (prototype_id) REFERENCES sylius_product_prototype (id)");
        $this->addSql("ALTER TABLE sylius_product_prototype_option ADD CONSTRAINT FK_1AD7AAC5A7C41D6F FOREIGN KEY (option_id) REFERENCES sylius_product_option (id)");
        $this->addSql("ALTER TABLE sylius_product_option_value ADD CONSTRAINT FK_F7FF7D4BA7C41D6F FOREIGN KEY (option_id) REFERENCES sylius_product_option (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE sylius_product_variant ADD CONSTRAINT FK_A29B5234584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE product_variant_option_value ADD CONSTRAINT FK_82F964363B69A9AF FOREIGN KEY (variant_id) REFERENCES sylius_product_variant (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE product_variant_option_value ADD CONSTRAINT FK_82F96436D957CA06 FOREIGN KEY (option_value_id) REFERENCES sylius_product_option_value (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE sylius_product_options ADD CONSTRAINT FK_2B5FF0094584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE sylius_product_options ADD CONSTRAINT FK_2B5FF009A7C41D6F FOREIGN KEY (option_id) REFERENCES sylius_product_option (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE sylius_product_variant_image ADD CONSTRAINT FK_C6B77D5D3B69A9AF FOREIGN KEY (variant_id) REFERENCES sylius_product_variant (id)");
        $this->addSql("ALTER TABLE sylius_inventory_unit DROP FOREIGN KEY FK_4A276986FBE8234");
        $this->addSql("ALTER TABLE sylius_inventory_unit ADD CONSTRAINT FK_4A276986FBE8234 FOREIGN KEY (stockable_id) REFERENCES sylius_product_variant (id)");
        $this->addSql("ALTER TABLE sylius_order_item DROP FOREIGN KEY FK_77B587ED3B69A9AF");
        $this->addSql("ALTER TABLE sylius_order_item ADD CONSTRAINT FK_77B587ED3B69A9AF FOREIGN KEY (variant_id) REFERENCES sylius_product_variant (id)");
    }

    public function down(Schema $schema)
    {
    }
}
