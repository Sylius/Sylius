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

final class Version20210809121349 extends AbstractMySqlMigration
{
    public function getDescription(): string
    {
        return 'Indexing of sylius_customer creation date';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX created_at_index ON sylius_customer (created_at DESC)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX created_at_index ON sylius_customer');
    }
}
