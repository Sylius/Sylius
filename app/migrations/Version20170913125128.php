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

        $this->addSql('ALTER TABLE sylius_order_item ADD immutable_product_name VARCHAR(255), ADD immutable_variant_name VARCHAR(255)');

        $this->addSql('UPDATE sylius_order_item 
                        INNER JOIN sylius_order AS o ON sylius_order_item.order_id = o.id 
                        INNER JOIN sylius_product_variant AS pv ON sylius_order_item.variant_id = pv.id 
                        INNER JOIN sylius_product AS p ON pv.product_id = p.id  
                        INNER JOIN sylius_product_translation AS pt ON pt.translatable_id = p.id 
                        INNER JOIN sylius_order AS ol ON pt.locale = ol.locale_code 
                        SET sylius_order_item.immutable_product_name =  pt.name
       
        ');
        $this->addSql('UPDATE sylius_order_item 
                        INNER JOIN sylius_order AS o ON sylius_order_item.order_id = o.id 
                        INNER JOIN sylius_product_variant AS pv ON sylius_order_item.variant_id = pv.id 
                        INNER JOIN sylius_product_variant_translation AS pvt ON pvt.translatable_id = pv.id 
                        INNER JOIN sylius_order AS ol ON pvt.locale = ol.locale_code 
                        SET sylius_order_item.immutable_variant_name =  pvt.name
        ');

        $this->addSql('ALTER TABLE sylius_order_item CHANGE immutable_product_name immutable_product_name VARCHAR(255) DEFAULT NULL, CHANGE immutable_variant_name immutable_variant_name VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_order_item DROP immutable_product_name, DROP immutable_variant_name');
    }
}
