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

namespace Sylius\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20170223071604 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_channel_pricing ADD channel_code VARCHAR(255) NOT NULL;');
        $this->addSql('UPDATE sylius_channel_pricing, sylius_channel SET sylius_channel_pricing.channel_code = sylius_channel.code WHERE sylius_channel.id = sylius_channel_pricing.channel_id');

        $this->addSql('ALTER TABLE sylius_channel_pricing DROP FOREIGN KEY FK_7801820C72F5A1AA');
        $this->addSql('DROP INDEX IDX_7801820C72F5A1AA ON sylius_channel_pricing');
        $this->addSql('DROP INDEX product_variant_channel_idx ON sylius_channel_pricing');
        $this->addSql('ALTER TABLE sylius_channel_pricing DROP channel_id');
        $this->addSql('CREATE UNIQUE INDEX product_variant_channel_idx ON sylius_channel_pricing (product_variant_id, channel_code)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX product_variant_channel_idx ON sylius_channel_pricing');
        $this->addSql('ALTER TABLE sylius_channel_pricing ADD channel_id INT NOT NULL');
        $this->addSql('UPDATE sylius_channel_pricing, sylius_channel SET sylius_channel_pricing.channel_id = sylius_channel.id WHERE sylius_channel.code = sylius_channel_pricing.channel_code');

        $this->addSql('ALTER TABLE sylius_channel_pricing DROP channel_code');
        $this->addSql('ALTER TABLE sylius_channel_pricing ADD CONSTRAINT FK_7801820C72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_7801820C72F5A1AA ON sylius_channel_pricing (channel_id)');
        $this->addSql('CREATE UNIQUE INDEX product_variant_channel_idx ON sylius_channel_pricing (product_variant_id, channel_id)');
    }
}
