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
class Version20151209104827 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_tax_category ADD code VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE sylius_tax_category s,
                           (SELECT @n := 0) m
                           SET s.`code` = CONCAT("TC", @n := @n + 1)         
                      ');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_221EB0BE77153098 ON sylius_tax_category (code)');
        $this->addSql('ALTER TABLE sylius_tax_rate ADD code VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE sylius_tax_rate s,
                           (SELECT @n := 0) m
                           SET s.`code` = CONCAT("TR", @n := @n + 1)         
                      ');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3CD86B2E77153098 ON sylius_tax_rate (code)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_221EB0BE77153098 ON sylius_tax_category');
        $this->addSql('ALTER TABLE sylius_tax_category DROP code');
        $this->addSql('DROP INDEX UNIQ_3CD86B2E77153098 ON sylius_tax_rate');
        $this->addSql('ALTER TABLE sylius_tax_rate DROP code');
    }
}
