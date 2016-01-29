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
class Version20151110183626 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product DROP FOREIGN KEY FK_677B9B749E2D1A41');
        $this->addSql('ALTER TABLE sylius_product DROP FOREIGN KEY FK_677B9B749DF894ED');
        $this->addSql('ALTER TABLE sylius_product DROP FOREIGN KEY FK_677B9B74E64AACD3');
        $this->addSql('ALTER TABLE sylius_product ADD CONSTRAINT FK_677B9B749E2D1A41 FOREIGN KEY (shipping_category_id) REFERENCES sylius_shipping_category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE sylius_product ADD CONSTRAINT FK_677B9B749DF894ED FOREIGN KEY (tax_category_id) REFERENCES sylius_tax_category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE sylius_product ADD CONSTRAINT FK_677B9B74E64AACD3 FOREIGN KEY (restricted_zone) REFERENCES sylius_zone (id) ON DELETE SET NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product DROP FOREIGN KEY FK_677B9B749DF894ED');
        $this->addSql('ALTER TABLE sylius_product DROP FOREIGN KEY FK_677B9B749E2D1A41');
        $this->addSql('ALTER TABLE sylius_product DROP FOREIGN KEY FK_677B9B74E64AACD3');
        $this->addSql('ALTER TABLE sylius_product ADD CONSTRAINT FK_677B9B749DF894ED FOREIGN KEY (tax_category_id) REFERENCES sylius_tax_category (id)');
        $this->addSql('ALTER TABLE sylius_product ADD CONSTRAINT FK_677B9B749E2D1A41 FOREIGN KEY (shipping_category_id) REFERENCES sylius_shipping_category (id)');
        $this->addSql('ALTER TABLE sylius_product ADD CONSTRAINT FK_677B9B74E64AACD3 FOREIGN KEY (restricted_zone) REFERENCES sylius_zone (id)');
    }
}
