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

final class Version20230331091850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initialize Price History';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(
            $schema->hasTable('sylius_channel_pricing_log_entry'),
            'Skipping migration: Sylius price history migrations were previously executed.',
        );

        $this->addSql('CREATE TABLE sylius_channel_price_history_config (id INT AUTO_INCREMENT NOT NULL, lowest_price_for_discounted_products_checking_period INT DEFAULT 30 NOT NULL, lowest_price_for_discounted_products_visible TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_channel_price_history_config_excluded_taxons (channel_id INT NOT NULL, taxon_id INT NOT NULL, INDEX IDX_77FD02A72F5A1AA (channel_id), INDEX IDX_77FD02ADE13F470 (taxon_id), PRIMARY KEY(channel_id, taxon_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_channel_pricing_log_entry (id INT AUTO_INCREMENT NOT NULL, channel_pricing_id INT NOT NULL, price INT NOT NULL, original_price INT DEFAULT NULL, logged_at DATETIME NOT NULL, INDEX IDX_77181A53EADFFE5 (channel_pricing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_channel_price_history_config_excluded_taxons ADD CONSTRAINT FK_77FD02A72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel_price_history_config (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_price_history_config_excluded_taxons ADD CONSTRAINT FK_77FD02ADE13F470 FOREIGN KEY (taxon_id) REFERENCES sylius_taxon (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_pricing_log_entry ADD CONSTRAINT FK_77181A53EADFFE5 FOREIGN KEY (channel_pricing_id) REFERENCES sylius_channel_pricing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel ADD channel_price_history_config_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_channel ADD CONSTRAINT FK_16C8119E75F20EAE FOREIGN KEY (channel_price_history_config_id) REFERENCES sylius_channel_price_history_config (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_16C8119E75F20EAE ON sylius_channel (channel_price_history_config_id)');
        $this->addSql('ALTER TABLE sylius_channel_pricing ADD lowest_price_before_discount INT DEFAULT NULL');

        /** Create an initial log state based on the price of products at the time of migration processing */
        $this->addSql('INSERT INTO `sylius_channel_pricing_log_entry` (`channel_pricing_id`, `price`, `original_price`, `logged_at`) SELECT `id`, `price`, `original_price`, NOW() FROM `sylius_channel_pricing`');
    }

    public function postUp(Schema $schema): void
    {
        $channelsIds = $this->connection->executeQuery('SELECT id from sylius_channel WHERE channel_price_history_config_id IS NULL')->fetchAllAssociative();
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
        $this->addSql('ALTER TABLE sylius_channel DROP FOREIGN KEY FK_16C8119E75F20EAE');
        $this->addSql('ALTER TABLE sylius_channel_price_history_config_excluded_taxons DROP FOREIGN KEY FK_77FD02A72F5A1AA');
        $this->addSql('ALTER TABLE sylius_channel_price_history_config_excluded_taxons DROP FOREIGN KEY FK_77FD02ADE13F470');
        $this->addSql('ALTER TABLE sylius_channel_pricing_log_entry DROP FOREIGN KEY FK_77181A53EADFFE5');
        $this->addSql('DROP TABLE sylius_channel_price_history_config');
        $this->addSql('DROP TABLE sylius_channel_price_history_config_excluded_taxons');
        $this->addSql('DROP TABLE sylius_channel_pricing_log_entry');
        $this->addSql('DROP INDEX UNIQ_16C8119E75F20EAE ON sylius_channel');
        $this->addSql('ALTER TABLE sylius_channel DROP channel_price_history_config_id');
        $this->addSql('ALTER TABLE sylius_channel_pricing DROP lowest_price_before_discount');
    }
}
