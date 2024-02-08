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

class Version20170201094058 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX product_image_code_idx ON sylius_product_image');
        $this->addSql('DROP INDEX taxon_image_code_idx ON sylius_taxon_image');
        $this->addSql('ALTER TABLE sylius_product_image CHANGE code `type` VARCHAR(255)');
        $this->addSql('ALTER TABLE sylius_taxon_image CHANGE code `type` VARCHAR(255)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_image CHANGE `type` code VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE sylius_taxon_image CHANGE `type` code VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX product_image_code_idx ON sylius_product_image (owner_id, code)');
        $this->addSql('CREATE UNIQUE INDEX taxon_image_code_idx ON sylius_taxon_image (owner_id, code)');
    }
}
