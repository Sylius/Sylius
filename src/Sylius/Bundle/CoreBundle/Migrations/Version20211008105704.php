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

final class Version20211008105704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add state to Catalog Promotion';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_catalog_promotion ADD state VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE sylius_catalog_promotion SET state = "inactive"');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_catalog_promotion DROP state');
    }
}
