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

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220210135918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add created_by_guest field to mark orders made by guests';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(!is_a($this->connection->getDatabasePlatform(), \Doctrine\DBAL\Platforms\MySqlPlatform::class, true), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_order ADD created_by_guest TINYINT(1) DEFAULT \'1\' NOT NULL');
        $this->addSql('UPDATE sylius_order o SET o.created_by_guest = 0 WHERE o.customer_id IN (SELECT customer_id FROM sylius_shop_user)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(!is_a($this->connection->getDatabasePlatform(), \Doctrine\DBAL\Platforms\MySqlPlatform::class, true), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_order DROP created_by_guest');
    }
}
