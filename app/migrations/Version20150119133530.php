<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migrate to custom product variant entity
 */
class Version20150119133530 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE sylius_inventory_unit DROP FOREIGN KEY FK_4A276986FBE8234');
        $this->addSql('ALTER TABLE sylius_order_item DROP FOREIGN KEY FK_77B587ED3B69A9AF');
        $this->addSql('ALTER TABLE sylius_product_variant_image DROP FOREIGN KEY FK_C6B77D5D3B69A9AF');
        $this->addSql('ALTER TABLE sylius_product_variant_option_value DROP FOREIGN KEY FK_76CDAFA13B69A9AF');

        $this->addSql(
            'CREATE TABLE smile_product_variant_scoped (id INT AUTO_INCREMENT NOT NULL, scope_aware_id INT NOT NULL, price INT NOT NULL, scope VARCHAR(255) NOT NULL, INDEX IDX_1F36FBB12A818313 (scope_aware_id), UNIQUE INDEX smile_product_variant_scoped_unique_scope (scope_aware_id, scope), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE smile_product_variant (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, is_master TINYINT(1) NOT NULL, presentation VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, available_on DATETIME NOT NULL, sku VARCHAR(255) DEFAULT NULL, on_hold INT NOT NULL, on_hand INT NOT NULL, sold INT NOT NULL, available_on_demand TINYINT(1) NOT NULL, price INT NOT NULL, pricing_calculator VARCHAR(255) NOT NULL, pricing_configuration LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', width DOUBLE PRECISION DEFAULT NULL, height DOUBLE PRECISION DEFAULT NULL, depth DOUBLE PRECISION DEFAULT NULL, weight DOUBLE PRECISION DEFAULT NULL, INDEX IDX_F3CECE804584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE smile_product_variant_scoped ADD CONSTRAINT FK_1F36FBB12A818313 FOREIGN KEY (scope_aware_id) REFERENCES smile_product_variant (id) ON DELETE CASCADE'
        );
        $this->addSql(
            'ALTER TABLE smile_product_variant ADD CONSTRAINT FK_F3CECE804584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE'
        );

        $this->addSql(
            'INSERT INTO smile_product_variant (id, product_id, is_master, presentation, created_at, updated_at, deleted_at, available_on, sku, on_hold, on_hand, sold, available_on_demand, price, pricing_calculator, pricing_configuration, width, height, depth, weight) SELECT id, product_id, is_master, presentation, created_at, updated_at, deleted_at, available_on, sku, on_hold, on_hand, sold, available_on_demand, price, pricing_calculator, pricing_configuration, width, height, depth, weight FROM sylius_product_variant '
        );

        $this->addSql('DROP TABLE sylius_product_variant');

        $this->addSql(
            'ALTER TABLE sylius_product_variant_image ADD CONSTRAINT FK_C6B77D5D3B69A9AF FOREIGN KEY (variant_id) REFERENCES smile_product_variant (id)'
        );
        $this->addSql(
            'ALTER TABLE sylius_order_item ADD CONSTRAINT FK_77B587ED3B69A9AF FOREIGN KEY (variant_id) REFERENCES smile_product_variant (id)'
        );
        $this->addSql(
            'ALTER TABLE sylius_inventory_unit ADD CONSTRAINT FK_4A276986FBE8234 FOREIGN KEY (stockable_id) REFERENCES smile_product_variant (id)'
        );
        $this->addSql(
            'ALTER TABLE sylius_product_variant_option_value ADD CONSTRAINT FK_76CDAFA13B69A9AF FOREIGN KEY (variant_id) REFERENCES smile_product_variant (id) ON DELETE CASCADE'
        );
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE sylius_product_variant_image DROP FOREIGN KEY FK_C6B77D5D3B69A9AF');
        $this->addSql('ALTER TABLE sylius_order_item DROP FOREIGN KEY FK_77B587ED3B69A9AF');
        $this->addSql('ALTER TABLE sylius_inventory_unit DROP FOREIGN KEY FK_4A276986FBE8234');
        $this->addSql('ALTER TABLE sylius_product_variant_option_value DROP FOREIGN KEY FK_76CDAFA13B69A9AF');
        $this->addSql('ALTER TABLE smile_product_variant_scoped DROP FOREIGN KEY FK_1F36FBB12A818313');

        $this->addSql(
            'CREATE TABLE sylius_product_variant (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, is_master TINYINT(1) NOT NULL, presentation VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, available_on DATETIME NOT NULL, sku VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, on_hold INT NOT NULL, on_hand INT NOT NULL, sold INT NOT NULL, available_on_demand TINYINT(1) NOT NULL, price INT NOT NULL, pricing_calculator VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, pricing_configuration LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\', width DOUBLE PRECISION DEFAULT NULL, height DOUBLE PRECISION DEFAULT NULL, depth DOUBLE PRECISION DEFAULT NULL, weight DOUBLE PRECISION DEFAULT NULL, INDEX IDX_A29B5234584665A (product_id), INDEX IDX_A29B523F9038C4 (sku), INDEX IDX_A29B52398D2DD99 (sold), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );

        $this->addSql(
            'ALTER TABLE sylius_product_variant ADD CONSTRAINT FK_A29B5234584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE'
        );

        $this->addSql(
            'INSERT INTO sylius_product_variant (id, product_id, is_master, presentation, created_at, updated_at, deleted_at, available_on, sku, on_hold, on_hand, sold, available_on_demand, price, pricing_calculator, pricing_configuration, width, height, depth, weight) SELECT id, product_id, is_master, presentation, created_at, updated_at, deleted_at, available_on, sku, on_hold, on_hand, sold, available_on_demand, price, pricing_calculator, pricing_configuration, width, height, depth, weight FROM smile_product_variant '
        );

        $this->addSql('DROP TABLE smile_product_variant_scoped');
        $this->addSql('DROP TABLE smile_product_variant');

        $this->addSql(
            'ALTER TABLE sylius_inventory_unit ADD CONSTRAINT FK_4A276986FBE8234 FOREIGN KEY (stockable_id) REFERENCES sylius_product_variant (id)'
        );
        $this->addSql(
            'ALTER TABLE sylius_order_item ADD CONSTRAINT FK_77B587ED3B69A9AF FOREIGN KEY (variant_id) REFERENCES sylius_product_variant (id)'
        );
        $this->addSql(
            'ALTER TABLE sylius_product_variant_image ADD CONSTRAINT FK_C6B77D5D3B69A9AF FOREIGN KEY (variant_id) REFERENCES sylius_product_variant (id)'
        );
        $this->addSql(
            'ALTER TABLE sylius_product_variant_option_value ADD CONSTRAINT FK_76CDAFA13B69A9AF FOREIGN KEY (variant_id) REFERENCES sylius_product_variant (id) ON DELETE CASCADE'
        );
    }
}
