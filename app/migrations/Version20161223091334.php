<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class Version20161223091334 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product_variant ADD position INT NOT NULL');
        $this->addSql('SET @row_number = -1');
        $this->addSql('CREATE TEMPORARY TABLE IF NOT EXISTS variants_count
                        SELECT sylius_product.id AS product_id, COUNT(sylius_product_variant.id) AS row_number FROM sylius_product
                        INNER JOIN sylius_product_variant ON sylius_product.id = sylius_product_variant.product_id
                        GROUP BY sylius_product.id'
        );
        $this->addSql('UPDATE sylius_product_variant
                        JOIN variants_count ON variants_count.product_id = sylius_product_variant.product_id
                        SET position = CASE
                                WHEN variants_count.row_number = 1 THEN (@row_number := -1) + 1
                                WHEN @row_number + 1 < variants_count.row_number then @row_number := @row_number + 1
                                ELSE @row_number := 0
                            END'
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product_variant DROP position');
    }
}
