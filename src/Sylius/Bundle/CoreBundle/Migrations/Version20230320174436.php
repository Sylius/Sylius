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

final class Version20230320174436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add excluded taxons and visibility of lowest price for discounted products to sylius_channel table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE sylius_channel_excluded_taxons (channel_id INT NOT NULL, taxon_id INT NOT NULL, INDEX IDX_3574E1E972F5A1AA (channel_id), INDEX IDX_3574E1E9DE13F470 (taxon_id), PRIMARY KEY(channel_id, taxon_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_channel_excluded_taxons ADD CONSTRAINT FK_3574E1E972F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_excluded_taxons ADD CONSTRAINT FK_3574E1E9DE13F470 FOREIGN KEY (taxon_id) REFERENCES sylius_taxon (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel ADD lowest_price_for_discounted_products_visible TINYINT(1) DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_channel_excluded_taxons DROP FOREIGN KEY FK_3574E1E972F5A1AA');
        $this->addSql('ALTER TABLE sylius_channel_excluded_taxons DROP FOREIGN KEY FK_3574E1E9DE13F470');
        $this->addSql('DROP TABLE sylius_channel_excluded_taxons');
        $this->addSql('ALTER TABLE sylius_channel DROP lowest_price_for_discounted_products_visible');
    }
}
