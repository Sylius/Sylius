<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211013060931 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Copy values from price to original price and make price optional but original price required';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE sylius_channel_pricing c SET c.original_price=c.price WHERE c.original_price is null');
    }

    public function down(Schema $schema): void
    {
        // todo
    }
}
