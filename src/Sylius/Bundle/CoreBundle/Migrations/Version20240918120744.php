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
use Doctrine\Migrations\AbstractMigration;

final class Version20240918120744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add position field to ProductImage entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_image ADD position INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_image DROP position');
    }
}
