<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211130110341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sylius_channel_pricing_applied_promotions (channel_pricing_id INT NOT NULL, catalog_promotion_id INT NOT NULL, INDEX IDX_EB2C22DC3EADFFE5 (channel_pricing_id), INDEX IDX_EB2C22DC22E2CB5A (catalog_promotion_id), PRIMARY KEY(channel_pricing_id, catalog_promotion_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_channel_pricing_applied_promotions ADD CONSTRAINT FK_EB2C22DC3EADFFE5 FOREIGN KEY (channel_pricing_id) REFERENCES sylius_channel_pricing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_pricing_applied_promotions ADD CONSTRAINT FK_EB2C22DC22E2CB5A FOREIGN KEY (catalog_promotion_id) REFERENCES sylius_catalog_promotion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_pricing DROP applied_promotions');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE sylius_channel_pricing_applied_promotions');
        $this->addSql('ALTER TABLE sylius_channel_pricing ADD applied_promotions JSON DEFAULT NULL');
    }
}
