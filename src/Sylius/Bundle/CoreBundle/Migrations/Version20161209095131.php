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

class Version20161209095131 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE sylius_zone SET scope = 'all' WHERE scope IS NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE sylius_zone SET scope = NULL WHERE scope = 'all'");
    }
}
