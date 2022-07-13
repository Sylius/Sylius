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

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210921093619 extends AbstractMySqlMigration
{
    public function getDescription(): string
    {
        return 'Make Catalog Promotion toggleable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_catalog_promotion ADD enabled TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_catalog_promotion DROP enabled');
    }
}
