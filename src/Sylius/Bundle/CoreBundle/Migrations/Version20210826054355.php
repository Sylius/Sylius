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

final class Version20210826054355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add channels to catalog promotion';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE sylius_catalog_promotion_channels (catalog_promotion_id INT NOT NULL, channel_id INT NOT NULL, INDEX IDX_48E9AE7622E2CB5A (catalog_promotion_id), INDEX IDX_48E9AE7672F5A1AA (channel_id), PRIMARY KEY(catalog_promotion_id, channel_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_catalog_promotion_channels ADD CONSTRAINT FK_48E9AE7622E2CB5A FOREIGN KEY (catalog_promotion_id) REFERENCES sylius_catalog_promotion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_catalog_promotion_channels ADD CONSTRAINT FK_48E9AE7672F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE sylius_catalog_promotion_channels');
    }
}
