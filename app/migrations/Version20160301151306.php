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
class Version20160301151306 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_product_attribute_value_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, presentation VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_BAE7B76D2C2AC5D3 (translatable_id), UNIQUE INDEX sylius_product_attribute_value_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_product_attribute_value_translation ADD CONSTRAINT FK_BAE7B76D2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES sylius_product_attribute_value (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_attribute_value DROP text_value');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sylius_product_attribute_value_translation');
        $this->addSql('ALTER TABLE sylius_product_attribute_value ADD text_value LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
