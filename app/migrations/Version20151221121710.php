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
class Version20151221121710 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product_option ADD code VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE sylius_product_option s,
                           (SELECT @n := 0) m
                           SET s.`code` = CONCAT("PO", @n := @n + 1)         
                      ');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E4C0EBEF77153098 ON sylius_product_option (code)');
        $this->addSql('ALTER TABLE sylius_product_option_value ADD code VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE sylius_product_option_value s,
                           (SELECT @n := 0) m
                           SET s.`code` = CONCAT("POV", @n := @n + 1)         
                      ');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F7FF7D4B77153098 ON sylius_product_option_value (code)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_E4C0EBEF77153098 ON sylius_product_option');
        $this->addSql('ALTER TABLE sylius_product_option DROP code');
        $this->addSql('DROP INDEX UNIQ_F7FF7D4B77153098 ON sylius_product_option_value');
        $this->addSql('ALTER TABLE sylius_product_option_value DROP code');
    }
}
