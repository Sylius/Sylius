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
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractPostgreSQLMigration;

final class Version20230420151332 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'This migration contains Sylius 1.13.0 changes.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE sylius_channel_price_history_config_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sylius_channel_pricing_log_entry_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sylius_promotion_translation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE sylius_channel_price_history_config (id INT NOT NULL, lowest_price_for_discounted_products_checking_period INT DEFAULT 30 NOT NULL, lowest_price_for_discounted_products_visible BOOLEAN DEFAULT true NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE sylius_channel_price_history_config_excluded_taxons (channel_id INT NOT NULL, taxon_id INT NOT NULL, PRIMARY KEY(channel_id, taxon_id))');
        $this->addSql('CREATE INDEX IDX_77FD02A72F5A1AA ON sylius_channel_price_history_config_excluded_taxons (channel_id)');
        $this->addSql('CREATE INDEX IDX_77FD02ADE13F470 ON sylius_channel_price_history_config_excluded_taxons (taxon_id)');
        $this->addSql('CREATE TABLE sylius_channel_pricing_log_entry (id INT NOT NULL, channel_pricing_id INT NOT NULL, price INT NOT NULL, original_price INT DEFAULT NULL, logged_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_77181A53EADFFE5 ON sylius_channel_pricing_log_entry (channel_pricing_id)');
        $this->addSql('CREATE TABLE sylius_promotion_translation (id INT NOT NULL, translatable_id INT NOT NULL, label VARCHAR(255) DEFAULT NULL, locale VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3C7A76182C2AC5D3 ON sylius_promotion_translation (translatable_id)');
        $this->addSql('CREATE UNIQUE INDEX sylius_promotion_translation_uniq_trans ON sylius_promotion_translation (translatable_id, locale)');
        $this->addSql('ALTER TABLE sylius_channel_price_history_config_excluded_taxons ADD CONSTRAINT FK_77FD02A72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel_price_history_config (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sylius_channel_price_history_config_excluded_taxons ADD CONSTRAINT FK_77FD02ADE13F470 FOREIGN KEY (taxon_id) REFERENCES sylius_taxon (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sylius_channel_pricing_log_entry ADD CONSTRAINT FK_77181A53EADFFE5 FOREIGN KEY (channel_pricing_id) REFERENCES sylius_channel_pricing (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sylius_promotion_translation ADD CONSTRAINT FK_3C7A76182C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES sylius_promotion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sylius_channel ADD channel_price_history_config_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_channel ADD CONSTRAINT FK_16C8119E75F20EAE FOREIGN KEY (channel_price_history_config_id) REFERENCES sylius_channel_price_history_config (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_16C8119E75F20EAE ON sylius_channel (channel_price_history_config_id)');
        $this->addSql('ALTER TABLE sylius_channel_pricing ADD lowest_price_before_discount INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_user_oauth ALTER access_token TYPE TEXT');
        $this->addSql('ALTER TABLE sylius_user_oauth ALTER refresh_token TYPE TEXT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_channel DROP CONSTRAINT FK_16C8119E75F20EAE');
        $this->addSql('DROP SEQUENCE sylius_channel_price_history_config_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sylius_channel_pricing_log_entry_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sylius_promotion_translation_id_seq CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_price_history_config_excluded_taxons DROP CONSTRAINT FK_77FD02A72F5A1AA');
        $this->addSql('ALTER TABLE sylius_channel_price_history_config_excluded_taxons DROP CONSTRAINT FK_77FD02ADE13F470');
        $this->addSql('ALTER TABLE sylius_channel_pricing_log_entry DROP CONSTRAINT FK_77181A53EADFFE5');
        $this->addSql('ALTER TABLE sylius_promotion_translation DROP CONSTRAINT FK_3C7A76182C2AC5D3');
        $this->addSql('DROP TABLE sylius_channel_price_history_config');
        $this->addSql('DROP TABLE sylius_channel_price_history_config_excluded_taxons');
        $this->addSql('DROP TABLE sylius_channel_pricing_log_entry');
        $this->addSql('DROP TABLE sylius_promotion_translation');
        $this->addSql('DROP INDEX UNIQ_16C8119E75F20EAE');
        $this->addSql('ALTER TABLE sylius_channel DROP channel_price_history_config_id');
        $this->addSql('ALTER TABLE sylius_user_oauth ALTER access_token TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE sylius_user_oauth ALTER refresh_token TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE sylius_channel_pricing DROP lowest_price_before_discount');
    }
}
