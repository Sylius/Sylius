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
class Version20160118160103 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product DROP FOREIGN KEY FK_677B9B749DF894ED');
        $this->addSql('DROP INDEX IDX_677B9B749DF894ED ON sylius_product');
        $this->addSql('ALTER TABLE sylius_product DROP tax_category_id');
        $this->addSql('ALTER TABLE sylius_product_variant ADD tax_category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_product_variant ADD CONSTRAINT FK_A29B5239DF894ED FOREIGN KEY (tax_category_id) REFERENCES sylius_tax_category (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_A29B5239DF894ED ON sylius_product_variant (tax_category_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product ADD tax_category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_product ADD CONSTRAINT FK_677B9B749DF894ED FOREIGN KEY (tax_category_id) REFERENCES sylius_tax_category (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_677B9B749DF894ED ON sylius_product (tax_category_id)');
        $this->addSql('ALTER TABLE sylius_product_variant DROP FOREIGN KEY FK_A29B5239DF894ED');
        $this->addSql('DROP INDEX IDX_A29B5239DF894ED ON sylius_product_variant');
        $this->addSql('ALTER TABLE sylius_product_variant DROP tax_category_id');
    }
}
