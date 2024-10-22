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

final class Version20211129213836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add a relation between ChannelPricings and applied on them CatalogPromotions';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE sylius_channel_pricing_catalog_promotions (channel_pricing_id INT NOT NULL, catalog_promotion_id INT NOT NULL, INDEX IDX_9F52FF513EADFFE5 (channel_pricing_id), INDEX IDX_9F52FF5122E2CB5A (catalog_promotion_id), PRIMARY KEY(channel_pricing_id, catalog_promotion_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_channel_pricing_catalog_promotions ADD CONSTRAINT FK_9F52FF513EADFFE5 FOREIGN KEY (channel_pricing_id) REFERENCES sylius_channel_pricing (id)');
        $this->addSql('ALTER TABLE sylius_channel_pricing_catalog_promotions ADD CONSTRAINT FK_9F52FF5122E2CB5A FOREIGN KEY (catalog_promotion_id) REFERENCES sylius_catalog_promotion (id)');
        $this->addSql('ALTER TABLE sylius_channel_pricing DROP applied_promotions');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE sylius_channel_pricing_catalog_promotions');
        $this->addSql('ALTER TABLE sylius_channel_pricing ADD applied_promotions JSON DEFAULT NULL');
    }
}
