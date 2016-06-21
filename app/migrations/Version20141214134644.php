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
class Version20141214134644 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product_prototype_attribute DROP FOREIGN KEY FK_E0C4700125998077');
        $this->addSql('ALTER TABLE sylius_product_prototype_option DROP FOREIGN KEY FK_1AD7AAC525998077');
        $this->addSql('CREATE TABLE sylius_product_archetype (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_A4001B52727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_product_archetype_option (product_archetype_id INT NOT NULL, option_id INT NOT NULL, INDEX IDX_BCE763A7FE884EAC (product_archetype_id), INDEX IDX_BCE763A7A7C41D6F (option_id), PRIMARY KEY(product_archetype_id, option_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_product_archetype_attribute (archetype_id INT NOT NULL, attribute_id INT NOT NULL, INDEX IDX_97763342732C6CC7 (archetype_id), INDEX IDX_97763342B6E62EFA (attribute_id), PRIMARY KEY(archetype_id, attribute_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_product_archetype ADD CONSTRAINT FK_A4001B52727ACA70 FOREIGN KEY (parent_id) REFERENCES sylius_product_archetype (id)');
        $this->addSql('ALTER TABLE sylius_product_archetype_option ADD CONSTRAINT FK_BCE763A7FE884EAC FOREIGN KEY (product_archetype_id) REFERENCES sylius_product_archetype (id)');
        $this->addSql('ALTER TABLE sylius_product_archetype_option ADD CONSTRAINT FK_BCE763A7A7C41D6F FOREIGN KEY (option_id) REFERENCES sylius_product_option (id)');
        $this->addSql('ALTER TABLE sylius_product_archetype_attribute ADD CONSTRAINT FK_97763342732C6CC7 FOREIGN KEY (archetype_id) REFERENCES sylius_product_archetype (id)');
        $this->addSql('ALTER TABLE sylius_product_archetype_attribute ADD CONSTRAINT FK_97763342B6E62EFA FOREIGN KEY (attribute_id) REFERENCES sylius_product_attribute (id)');
        $this->addSql('DROP TABLE sylius_product_prototype');
        $this->addSql('DROP TABLE sylius_product_prototype_attribute');
        $this->addSql('DROP TABLE sylius_product_prototype_option');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product_archetype DROP FOREIGN KEY FK_A4001B52727ACA70');
        $this->addSql('ALTER TABLE sylius_product_archetype_option DROP FOREIGN KEY FK_BCE763A7FE884EAC');
        $this->addSql('ALTER TABLE sylius_product_archetype_attribute DROP FOREIGN KEY FK_97763342732C6CC7');
        $this->addSql('CREATE TABLE sylius_product_prototype (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_product_prototype_attribute (prototype_id INT NOT NULL, attribute_id INT NOT NULL, INDEX IDX_E0C4700125998077 (prototype_id), INDEX IDX_E0C47001B6E62EFA (attribute_id), PRIMARY KEY(prototype_id, attribute_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_product_prototype_option (prototype_id INT NOT NULL, option_id INT NOT NULL, INDEX IDX_1AD7AAC525998077 (prototype_id), INDEX IDX_1AD7AAC5A7C41D6F (option_id), PRIMARY KEY(prototype_id, option_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_product_prototype_attribute ADD CONSTRAINT FK_E0C47001B6E62EFA FOREIGN KEY (attribute_id) REFERENCES sylius_product_attribute (id)');
        $this->addSql('ALTER TABLE sylius_product_prototype_attribute ADD CONSTRAINT FK_E0C4700125998077 FOREIGN KEY (prototype_id) REFERENCES sylius_product_prototype (id)');
        $this->addSql('ALTER TABLE sylius_product_prototype_option ADD CONSTRAINT FK_1AD7AAC5A7C41D6F FOREIGN KEY (option_id) REFERENCES sylius_product_option (id)');
        $this->addSql('ALTER TABLE sylius_product_prototype_option ADD CONSTRAINT FK_1AD7AAC525998077 FOREIGN KEY (prototype_id) REFERENCES sylius_product_prototype (id)');
        $this->addSql('DROP TABLE sylius_product_archetype');
        $this->addSql('DROP TABLE sylius_product_archetype_option');
        $this->addSql('DROP TABLE sylius_product_archetype_attribute');
    }
}
