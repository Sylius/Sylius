<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20170913125128 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_order_item ADD product_name VARCHAR(255), ADD variant_name VARCHAR(255)');

        $this->addSql('
                        UPDATE sylius_order_item 
                        INNER JOIN sylius_order ON sylius_order_item.order_id = sylius_order.id 
                        INNER JOIN sylius_product_variant ON sylius_order_item.variant_id = sylius_product_variant.id 
                        INNER JOIN sylius_product ON sylius_product_variant.product_id = sylius_product.id  
                        INNER JOIN sylius_product_translation ON sylius_product_translation.translatable_id = sylius_product.id
                        SET sylius_order_item.product_name = sylius_product_translation.name 
                        WHERE sylius_product_translation.locale = sylius_order.locale_code
       
        ');
        $this->addSql('
                        UPDATE sylius_order_item 
                        INNER JOIN sylius_order ON sylius_order_item.order_id = sylius_order.id 
                        INNER JOIN sylius_product_variant ON sylius_order_item.variant_id = sylius_product_variant.id 
                        INNER JOIN sylius_product_variant_translation ON sylius_product_variant_translation.translatable_id = sylius_product_variant.id
                        SET sylius_order_item.variant_name = sylius_product_variant_translation.name 
                        WHERE sylius_product_variant_translation.locale = sylius_order.locale_code
        ');

        $this->addSql('ALTER TABLE sylius_order_item CHANGE product_name product_name VARCHAR(255) DEFAULT NULL, CHANGE variant_name variant_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_order_item DROP product_name, DROP variant_name');
    }
}
