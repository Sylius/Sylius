<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150202213852 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->connection->executeQuery('CREATE TABLE sylius_channel (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, enabled TINYINT(1) NOT NULL, url VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_16C8119E77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->connection->insert('sylius_channel', [
            'code' => 'WEB',
            'name' => 'Default Web',
            'color' => 'green',
            'enabled' => true,
            'created_at' => 'NOW()',
            'updated_at' => 'NOW()',
        ]);
        $channelId = $this->connection->lastInsertId();

        $this->connection->executeQuery('CREATE TABLE sylius_product_channels (product_id INT NOT NULL, channel_id INT NOT NULL, INDEX IDX_F9EF269B4584665A (product_id), INDEX IDX_F9EF269B72F5A1AA (channel_id), PRIMARY KEY(product_id, channel_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->connection->executeQuery("INSERT INTO sylius_product_channels (product_id, channel_id) SELECT id as product_id, {$channelId} as channel_id FROM sylius_product");

        $this->connection->executeQuery('CREATE TABLE sylius_promotion_channels (promotion_id INT NOT NULL, channel_id INT NOT NULL, INDEX IDX_1A044F64139DF194 (promotion_id), INDEX IDX_1A044F6472F5A1AA (channel_id), PRIMARY KEY(promotion_id, channel_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->connection->executeQuery("INSERT INTO sylius_promotion_channels (promotion_id, channel_id) SELECT id as promotion_id, {$channelId} as channel_id FROM sylius_promotion");

        $this->connection->executeQuery('CREATE TABLE sylius_channel_currencies (channel_id INT NOT NULL, currency_id INT NOT NULL, INDEX IDX_AE491F9372F5A1AA (channel_id), INDEX IDX_AE491F9338248176 (currency_id), PRIMARY KEY(channel_id, currency_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->connection->executeQuery("INSERT INTO sylius_channel_currencies (currency_id, channel_id) SELECT id as currency_id, {$channelId} as channel_id FROM sylius_currency");

        $this->connection->executeQuery('CREATE TABLE sylius_channel_locales (channel_id INT NOT NULL, locale_id INT NOT NULL, INDEX IDX_786B7A8472F5A1AA (channel_id), INDEX IDX_786B7A84E559DFD1 (locale_id), PRIMARY KEY(channel_id, locale_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->connection->executeQuery("INSERT INTO sylius_channel_locales (locale_id, channel_id) SELECT id as locale_id, {$channelId} as channel_id FROM sylius_locale");

        $this->connection->executeQuery('CREATE TABLE sylius_channel_shipping_methods (channel_id INT NOT NULL, shipping_method_id INT NOT NULL, INDEX IDX_6858B18E72F5A1AA (channel_id), INDEX IDX_6858B18E5F7D6850 (shipping_method_id), PRIMARY KEY(channel_id, shipping_method_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->connection->executeQuery("INSERT INTO sylius_channel_shipping_methods (shipping_method_id, channel_id) SELECT id as shipping_method_id, {$channelId} as channel_id FROM sylius_shipping_method");

        $this->connection->executeQuery('CREATE TABLE sylius_channel_payment_methods (channel_id INT NOT NULL, payment_method_id INT NOT NULL, INDEX IDX_B0C0002B72F5A1AA (channel_id), INDEX IDX_B0C0002B5AA1164F (payment_method_id), PRIMARY KEY(channel_id, payment_method_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->connection->executeQuery("INSERT INTO sylius_channel_payment_methods (payment_method_id, channel_id) SELECT id as payment_method_id, {$channelId} as channel_id FROM sylius_payment_method");

        $this->connection->executeQuery('CREATE TABLE sylius_product_taxonomy (channel_id INT NOT NULL, taxonomy_id INT NOT NULL, INDEX IDX_F7E97C1072F5A1AA (channel_id), INDEX IDX_F7E97C109557E6F6 (taxonomy_id), PRIMARY KEY(channel_id, taxonomy_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->connection->executeQuery("INSERT INTO sylius_product_taxonomy (taxonomy_id, channel_id) SELECT id as taxonomy_id, {$channelId} as channel_id FROM sylius_taxonomy");

        $this->connection->executeQuery('ALTER TABLE sylius_order ADD channel_id INT DEFAULT NULL');
        $this->connection->executeQuery("UPDATE sylius_order SET channel_id = {$channelId}");

        $this->addSql('ALTER TABLE sylius_product_channels ADD CONSTRAINT FK_F9EF269B4584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_channels ADD CONSTRAINT FK_F9EF269B72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_promotion_channels ADD CONSTRAINT FK_1A044F64139DF194 FOREIGN KEY (promotion_id) REFERENCES sylius_promotion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_promotion_channels ADD CONSTRAINT FK_1A044F6472F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_currencies ADD CONSTRAINT FK_AE491F9372F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_currencies ADD CONSTRAINT FK_AE491F9338248176 FOREIGN KEY (currency_id) REFERENCES sylius_currency (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_locales ADD CONSTRAINT FK_786B7A8472F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_locales ADD CONSTRAINT FK_786B7A84E559DFD1 FOREIGN KEY (locale_id) REFERENCES sylius_locale (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_shipping_methods ADD CONSTRAINT FK_6858B18E72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_shipping_methods ADD CONSTRAINT FK_6858B18E5F7D6850 FOREIGN KEY (shipping_method_id) REFERENCES sylius_shipping_method (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_payment_methods ADD CONSTRAINT FK_B0C0002B72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_payment_methods ADD CONSTRAINT FK_B0C0002B5AA1164F FOREIGN KEY (payment_method_id) REFERENCES sylius_payment_method (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_taxonomy ADD CONSTRAINT FK_F7E97C1072F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_taxonomy ADD CONSTRAINT FK_F7E97C109557E6F6 FOREIGN KEY (taxonomy_id) REFERENCES sylius_taxonomy (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE sylius_order ADD CONSTRAINT FK_6196A1F972F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)');
        $this->addSql('CREATE INDEX IDX_6196A1F972F5A1AA ON sylius_order (channel_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product_channels DROP FOREIGN KEY FK_F9EF269B72F5A1AA');
        $this->addSql('ALTER TABLE sylius_promotion_channels DROP FOREIGN KEY FK_1A044F6472F5A1AA');
        $this->addSql('ALTER TABLE sylius_order DROP FOREIGN KEY FK_6196A1F972F5A1AA');
        $this->addSql('ALTER TABLE sylius_channel_currencies DROP FOREIGN KEY FK_AE491F9372F5A1AA');
        $this->addSql('ALTER TABLE sylius_channel_locales DROP FOREIGN KEY FK_786B7A8472F5A1AA');
        $this->addSql('ALTER TABLE sylius_channel_shipping_methods DROP FOREIGN KEY FK_6858B18E72F5A1AA');
        $this->addSql('ALTER TABLE sylius_channel_payment_methods DROP FOREIGN KEY FK_B0C0002B72F5A1AA');
        $this->addSql('ALTER TABLE sylius_product_taxonomy DROP FOREIGN KEY FK_F7E97C1072F5A1AA');
        $this->addSql('DROP TABLE sylius_product_channels');
        $this->addSql('DROP TABLE sylius_promotion_channels');
        $this->addSql('DROP TABLE sylius_channel');
        $this->addSql('DROP TABLE sylius_channel_currencies');
        $this->addSql('DROP TABLE sylius_channel_locales');
        $this->addSql('DROP TABLE sylius_channel_shipping_methods');
        $this->addSql('DROP TABLE sylius_channel_payment_methods');
        $this->addSql('DROP TABLE sylius_product_taxonomy');
        $this->addSql('DROP INDEX IDX_6196A1F972F5A1AA ON sylius_order');
        $this->addSql('ALTER TABLE sylius_order DROP channel_id');
    }
}
