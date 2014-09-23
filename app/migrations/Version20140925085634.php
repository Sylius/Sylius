<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140925085634 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product ADD master_variant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_product ADD CONSTRAINT FK_677B9B744A37A82F FOREIGN KEY (master_variant_id) REFERENCES sylius_product_variant (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_677B9B744A37A82F ON sylius_product (master_variant_id)');
        $this->addSql('ALTER TABLE sylius_promotion_coupon ADD per_user_usage_limit INT DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product DROP FOREIGN KEY FK_677B9B744A37A82F');
        $this->addSql('DROP INDEX UNIQ_677B9B744A37A82F ON sylius_product');
        $this->addSql('ALTER TABLE sylius_product DROP master_variant_id');
        $this->addSql('ALTER TABLE sylius_promotion_coupon DROP per_user_usage_limit');
    }
}
