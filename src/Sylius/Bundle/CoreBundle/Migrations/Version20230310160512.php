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

final class Version20230310160512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add ChannelPricingLogEntry';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE sylius_channel_pricing_log_entry (id INT AUTO_INCREMENT NOT NULL, channel_pricing_id INT NOT NULL, price INT NOT NULL, original_price INT DEFAULT NULL, logged_at DATETIME NOT NULL, INDEX IDX_77181A53EADFFE5 (channel_pricing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_channel_pricing_log_entry ADD CONSTRAINT FK_77181A53EADFFE5 FOREIGN KEY (channel_pricing_id) REFERENCES sylius_channel_pricing (id) ON DELETE CASCADE');

        /** Create an initial log state based on the price of products at the time of migration processing */
        $this->addSql('INSERT INTO `sylius_channel_pricing_log_entry` (`channel_pricing_id`, `price`, `original_price`, `logged_at`) SELECT `id`, `price`, `original_price`, NOW() FROM `sylius_channel_pricing`');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_channel_pricing_log_entry DROP FOREIGN KEY FK_77181A53EADFFE5');
        $this->addSql('DROP TABLE sylius_channel_pricing_log_entry');
    }
}
