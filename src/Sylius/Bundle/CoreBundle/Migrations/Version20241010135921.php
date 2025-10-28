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

final class Version20241010135921 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add priority property to the Zone';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_zone ADD priority INT NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_zone DROP priority');
    }
}
