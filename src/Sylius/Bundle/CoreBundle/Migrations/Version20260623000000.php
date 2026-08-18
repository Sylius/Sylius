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

final class Version20260623000000 extends AbstractMigration
{
    private const TABLE_NAME = 'sylius_product_option_value';

    public function getDescription(): string
    {
        return 'Add position column to sylius_product_option_value (MySQL)';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable(self::TABLE_NAME)) {
            return;
        }

        $table = $schema->getTable(self::TABLE_NAME);

        if ($table->hasColumn('position')) {
            return;
        }

        $this->addSql('ALTER TABLE ' . self::TABLE_NAME . ' ADD position INT NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        if (!$schema->hasTable(self::TABLE_NAME)) {
            return;
        }

        $table = $schema->getTable(self::TABLE_NAME);

        if (!$table->hasColumn('position')) {
            return;
        }

        $this->addSql('ALTER TABLE ' . self::TABLE_NAME . ' DROP COLUMN position');
    }
}
