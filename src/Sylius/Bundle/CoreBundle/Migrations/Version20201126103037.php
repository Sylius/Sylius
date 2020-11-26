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

final class Version20201126103037 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add details to adjustment';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_adjustment ADD details LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_adjustment DROP details');
    }
}
