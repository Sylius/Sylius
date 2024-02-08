<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

class Version20161223091334 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_variant ADD position INT NOT NULL');
        $this->addSql('SET @row_number = -1');
        $this->addSql('CREATE TEMPORARY TABLE IF NOT EXISTS variants_count
                        SELECT sylius_product.id AS product_id, COUNT(sylius_product_variant.id) AS `row_number` FROM sylius_product
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

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_variant DROP position');
    }
}
