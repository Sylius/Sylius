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

class Version20161221133514 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_option ADD position INT NOT NULL');
        $this->addSql('SET @row_number = -1');
        $this->addSql('UPDATE sylius_product_option SET position = @row_number := @row_number + 1');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_option DROP position');
    }
}
