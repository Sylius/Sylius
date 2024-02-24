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

final class Version20200122082429 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE sylius_channel_countries (channel_id INT NOT NULL, country_id INT NOT NULL, INDEX IDX_D96E51AE72F5A1AA (channel_id), INDEX IDX_D96E51AEF92F3E70 (country_id), PRIMARY KEY(channel_id, country_id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_channel_countries ADD CONSTRAINT FK_D96E51AE72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_countries ADD CONSTRAINT FK_D96E51AEF92F3E70 FOREIGN KEY (country_id) REFERENCES sylius_country (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE sylius_channel_countries');
    }
}
