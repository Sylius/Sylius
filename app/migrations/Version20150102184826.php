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
class Version20150102184826 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product_attribute_value CHANGE value value LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_product ADD archetype_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_product ADD CONSTRAINT FK_677B9B74732C6CC7 FOREIGN KEY (archetype_id) REFERENCES sylius_product_archetype (id)');
        $this->addSql('CREATE INDEX IDX_677B9B74732C6CC7 ON sylius_product (archetype_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product DROP FOREIGN KEY FK_677B9B74732C6CC7');
        $this->addSql('DROP INDEX IDX_677B9B74732C6CC7 ON sylius_product');
        $this->addSql('ALTER TABLE sylius_product DROP archetype_id');
        $this->addSql('ALTER TABLE sylius_product_attribute_value CHANGE value value LONGTEXT NOT NULL COLLATE utf8_unicode_ci');
    }
}
