<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161123094938 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_channel_pricing (id INT AUTO_INCREMENT NOT NULL, product_variant_id INT NOT NULL, channel_id INT NOT NULL, price INT NOT NULL, INDEX IDX_7801820CA80EF684 (product_variant_id), INDEX IDX_7801820C72F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_channel_pricing ADD CONSTRAINT FK_7801820CA80EF684 FOREIGN KEY (product_variant_id) REFERENCES sylius_product_variant (id)');
        $this->addSql('ALTER TABLE sylius_channel_pricing ADD CONSTRAINT FK_7801820C72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)');
        $this->addSql('ALTER TABLE sylius_product_variant DROP price, DROP pricing_calculator, DROP pricing_configuration');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sylius_channel_pricing');
        $this->addSql('ALTER TABLE sylius_product_variant ADD price INT NOT NULL, ADD pricing_calculator VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD pricing_configuration LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\'');
    }
}
