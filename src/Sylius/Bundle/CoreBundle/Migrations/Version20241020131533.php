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
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractPostgreSQLMigration;

final class Version20241020131533 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Add position field to ProductImage entity and set positions for existing images.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_image ADD position INT DEFAULT 0');

        $this->addSql(
            'CREATE TEMPORARY TABLE image_variants_count AS
                        SELECT sylius_product_image.id AS image_id, sylius_product_variant.product_id, COUNT(sylius_product_variant.id) AS variant_count
                        FROM sylius_product_image
                        JOIN sylius_product_image_product_variants AS piv ON sylius_product_image.id = piv.image_id
                        JOIN sylius_product_variant ON piv.variant_id = sylius_product_variant.id
                        GROUP BY sylius_product_image.id, sylius_product_variant.product_id',
        );

        $this->addSql(
            'WITH ranked_images AS (
                            SELECT sylius_product_image.id AS image_id,
                                   sylius_product_image.owner_id,
                                   ROW_NUMBER() OVER (PARTITION BY sylius_product_image.owner_id ORDER BY sylius_product_image.id) - 1 AS position_rank
                            FROM sylius_product_image
                        )
                        UPDATE sylius_product_image
                        SET position = ranked_images.position_rank
                        FROM ranked_images
                        WHERE sylius_product_image.id = ranked_images.image_id',
        );

        $this->addSql('ALTER TABLE sylius_product_image ALTER COLUMN position SET NOT NULL');
        $this->addSql('ALTER TABLE sylius_product_image ALTER COLUMN position DROP DEFAULT');

        $this->addSql('DROP TABLE image_variants_count');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_image DROP position');
    }
}
