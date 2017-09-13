<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170913125128 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_order_item ADD immutable_product_name VARCHAR(255), ADD immutable_product_code VARCHAR(255), ADD immutable_variant_name VARCHAR(255), ADD immutable_variant_code VARCHAR(255)');

        $this->addSql('update sylius_order_item INNER JOIN sylius_order as o ON sylius_order_item.order_id = o.id INNER JOIN sylius_product_variant as pv1 ON sylius_order_item.variant_id = pv1.id INNER JOIN sylius_product as p1 ON pv1.product_id = p1.id  INNER JOIN sylius_product_translation as pt1 ON pt1.translatable_id = p1.id INNER JOIN sylius_order as o2 on pt1.locale = o2.locale_code set sylius_order_item.immutable_product_name =  pt1.name,  sylius_order_item.immutable_product_code = p1.code');

        $this->addSql('update sylius_order_item INNER JOIN sylius_order as o ON sylius_order_item.order_id = o.id INNER JOIN sylius_product_variant as pv1 ON sylius_order_item.variant_id = pv1.id INNER JOIN sylius_product_variant_translation as pt1 ON pt1.translatable_id = pv1.id INNER JOIN sylius_order as o2 on pt1.locale = o2.locale_code set sylius_order_item.immutable_variant_name =  pt1.name,  sylius_order_item.immutable_variant_code = pv1.code');

        $this->addSql('ALTER TABLE sylius_order_item CHANGE immutable_product_name immutable_product_name VARCHAR(255) DEFAULT NULL, CHANGE immutable_product_code immutable_product_code VARCHAR(255) DEFAULT NULL, CHANGE immutable_variant_name immutable_variant_name VARCHAR(255) DEFAULT NULL, CHANGE immutable_variant_code immutable_variant_code VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_order_item DROP immutable_product_name, DROP immutable_product_code, DROP immutable_variant_name, DROP immutable_variant_code');
    }
}
