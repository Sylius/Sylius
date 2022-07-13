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
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMySqlMigration;

class Version20180102140039 extends AbstractMySqlMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX IDX_16C8119EE551C011 ON sylius_channel (hostname)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_16C8119EE551C011 ON sylius_channel');
    }
}
