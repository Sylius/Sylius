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

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

final class Version20241020131510 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add position field to ProductImage entity and set positions for existing images.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_image ADD position INT NOT NULL');

        $this->addSql('SET @row_number = 0;');

        $this->addSql('CREATE TEMPORARY TABLE IF NOT EXISTS image_count_per_product AS
                        SELECT sylius_product_image.owner_id, sylius_product_image.id AS image_id
                        FROM sylius_product_image
                        ORDER BY sylius_product_image.owner_id, sylius_product_image.id');

        $this->addSql('UPDATE sylius_product_image
                        JOIN (
                            SELECT owner_id, image_id, (@row_number := IF(@current_product = owner_id, @row_number + 1, 0)) AS position, @current_product := owner_id
                            FROM image_count_per_product
                        ) AS ranked_images ON ranked_images.image_id = sylius_product_image.id
                        SET sylius_product_image.position = ranked_images.position');

        $this->addSql('DROP TEMPORARY TABLE image_count_per_product;');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_image DROP position');
    }
}
