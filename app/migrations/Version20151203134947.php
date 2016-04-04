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
class Version20151203134947 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_shipping_category ADD code VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE sylius_shipping_category s,
                           (SELECT @n := 0) m
                           SET s.`code` = CONCAT("SC", @n := @n + 1)         
                      ');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B1D6465277153098 ON sylius_shipping_category (code)');
        $this->addSql('ALTER TABLE sylius_shipping_method ADD code VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE sylius_shipping_method s,
                           (SELECT @n := 0) m
                           SET s.`code` = CONCAT("SM", @n := @n + 1)         
                      ');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5FB0EE1177153098 ON sylius_shipping_method (code)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_B1D6465277153098 ON sylius_shipping_category');
        $this->addSql('ALTER TABLE sylius_shipping_category DROP code');
        $this->addSql('DROP INDEX UNIQ_5FB0EE1177153098 ON sylius_shipping_method');
        $this->addSql('ALTER TABLE sylius_shipping_method DROP code');
    }
}
