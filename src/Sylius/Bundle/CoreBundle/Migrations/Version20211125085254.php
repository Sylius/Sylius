<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211125085254 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Set default value on minimum_price';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_channel_pricing CHANGE minimum_price minimum_price INT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_channel_pricing CHANGE minimum_price minimum_price INT NOT NULL');
    }
}
