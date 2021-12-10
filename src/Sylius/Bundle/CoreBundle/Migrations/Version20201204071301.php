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

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201204071301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add details to adjustment';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $adjustmentTable = $schema->getTable('sylius_adjustment');
        if ($adjustmentTable->hasColumn('details')) {
            return;
        }

        $this->addSql('ALTER TABLE sylius_adjustment ADD details JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $adjustmentTable = $schema->getTable('sylius_adjustment');
        if (!$adjustmentTable->hasColumn('details')) {
            return;
        }

        $this->addSql('ALTER TABLE sylius_adjustment DROP details');
    }
}
