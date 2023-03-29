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

final class Version20230329093932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add ChannelPriceHistoryConfig';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE sylius_channel_price_history_config (id INT AUTO_INCREMENT NOT NULL, lowest_price_for_discounted_products_checking_period INT DEFAULT 30 NOT NULL, lowest_price_for_discounted_products_visible TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_channel_price_history_config_excluded_taxons (channel_id INT NOT NULL, taxon_id INT NOT NULL, INDEX IDX_77FD02A72F5A1AA (channel_id), INDEX IDX_77FD02ADE13F470 (taxon_id), PRIMARY KEY(channel_id, taxon_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_channel_price_history_config_excluded_taxons ADD CONSTRAINT FK_77FD02A72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel_price_history_config (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_price_history_config_excluded_taxons ADD CONSTRAINT FK_77FD02ADE13F470 FOREIGN KEY (taxon_id) REFERENCES sylius_taxon (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_excluded_taxons DROP FOREIGN KEY FK_3574E1E972F5A1AA');
        $this->addSql('ALTER TABLE sylius_channel_excluded_taxons DROP FOREIGN KEY FK_3574E1E9DE13F470');
        $this->addSql('DROP TABLE sylius_channel_excluded_taxons');
        $this->addSql('ALTER TABLE sylius_channel ADD channel_price_history_config_id INT DEFAULT NULL, DROP lowest_price_for_discounted_products_checking_period, DROP lowest_price_for_discounted_products_visible');
        $this->addSql('ALTER TABLE sylius_channel ADD CONSTRAINT FK_16C8119E75F20EAE FOREIGN KEY (channel_price_history_config_id) REFERENCES sylius_channel_price_history_config (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_16C8119E75F20EAE ON sylius_channel (channel_price_history_config_id)');
    }

    public function postUp(Schema $schema): void
    {
        $channelsIds = $this->connection->executeQuery('SELECT id from sylius_channel')->fetchAllAssociative();
        foreach ($channelsIds as $channelId) {
            $this->connection->executeQuery('INSERT INTO sylius_channel_price_history_config (lowest_price_for_discounted_products_checking_period, lowest_price_for_discounted_products_visible) VALUES (30, true)');
            $this->connection->executeQuery('UPDATE sylius_channel SET channel_price_history_config_id = :priceHistoryConfig WHERE id = :channel', [
                'channel' => $channelId['id'],
                'priceHistoryConfig' => $this->connection->lastInsertId(),
            ]);
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sylius_channel DROP FOREIGN KEY FK_16C8119E75F20EAE');
        $this->addSql('CREATE TABLE sylius_channel_excluded_taxons (channel_id INT NOT NULL, taxon_id INT NOT NULL, INDEX IDX_3574E1E972F5A1AA (channel_id), INDEX IDX_3574E1E9DE13F470 (taxon_id), PRIMARY KEY(channel_id, taxon_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE sylius_channel_excluded_taxons ADD CONSTRAINT FK_3574E1E972F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_excluded_taxons ADD CONSTRAINT FK_3574E1E9DE13F470 FOREIGN KEY (taxon_id) REFERENCES sylius_taxon (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_price_history_config_excluded_taxons DROP FOREIGN KEY FK_77FD02A72F5A1AA');
        $this->addSql('ALTER TABLE sylius_channel_price_history_config_excluded_taxons DROP FOREIGN KEY FK_77FD02ADE13F470');
        $this->addSql('DROP TABLE sylius_channel_price_history_config');
        $this->addSql('DROP TABLE sylius_channel_price_history_config_excluded_taxons');
        $this->addSql('DROP INDEX UNIQ_16C8119E75F20EAE ON sylius_channel');
        $this->addSql('ALTER TABLE sylius_channel ADD lowest_price_for_discounted_products_checking_period INT DEFAULT 30 NOT NULL, ADD lowest_price_for_discounted_products_visible TINYINT(1) DEFAULT 1 NOT NULL, DROP channel_price_history_config_id');
    }
}
