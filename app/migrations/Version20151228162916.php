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
class Version20151228162916 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product_attribute ADD storage_type VARCHAR(255) NOT NULL, CHANGE name code VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE sylius_product_attribute s,
                           (SELECT @n := 0) m
                           SET s.`code` = CONCAT("PA", @n := @n + 1)         
                      ');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BFAF484A77153098 ON sylius_product_attribute (code)');
        $this->addSql('ALTER TABLE sylius_product_attribute_translation CHANGE presentation name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE sylius_product_attribute_value ADD boolean_value TINYINT(1) DEFAULT NULL, ADD integer_value INT DEFAULT NULL, ADD float_value DOUBLE PRECISION DEFAULT NULL, ADD datetime_value DATETIME DEFAULT NULL, ADD date_value DATE DEFAULT NULL, CHANGE value text_value LONGTEXT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_BFAF484A77153098 ON sylius_product_attribute');
        $this->addSql('ALTER TABLE sylius_product_attribute ADD name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP code, DROP storage_type');
        $this->addSql('ALTER TABLE sylius_product_attribute_translation CHANGE name presentation VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE sylius_product_attribute_value DROP boolean_value, DROP integer_value, DROP float_value, DROP datetime_value, DROP date_value, CHANGE text_value value LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
