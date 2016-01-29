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
class Version20160115104455 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_shipping_method ADD tax_category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_shipping_method ADD CONSTRAINT FK_5FB0EE119DF894ED FOREIGN KEY (tax_category_id) REFERENCES sylius_tax_category (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_5FB0EE119DF894ED ON sylius_shipping_method (tax_category_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_shipping_method DROP FOREIGN KEY FK_5FB0EE119DF894ED');
        $this->addSql('DROP INDEX IDX_5FB0EE119DF894ED ON sylius_shipping_method');
        $this->addSql('ALTER TABLE sylius_shipping_method DROP tax_category_id');
    }
}
