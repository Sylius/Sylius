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
use Sylius\Component\Addressing\Model\Scope;

class Version20161209095131 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(sprintf("UPDATE sylius_zone SET scope = '%s' where scope IS NULL", Scope::ALL));
    }

    public function down(Schema $schema): void
    {
        $this->addSql(sprintf("UPDATE sylius_zone SET scope = NULL where scope = '%s'", Scope::ALL));
    }
}
