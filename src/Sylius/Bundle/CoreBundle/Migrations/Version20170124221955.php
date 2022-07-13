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
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMySqlMigration;

class Version20170124221955 extends AbstractMySqlMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_locale DROP enabled');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_locale ADD enabled TINYINT(1) NOT NULL');
    }
}
