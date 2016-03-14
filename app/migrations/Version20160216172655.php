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
class Version20160216172655 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_product_association (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, association_type_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_48E9CDAB4584665A (product_id), INDEX IDX_48E9CDABB1E1C39 (association_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_product_association_product (association_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_A427B983EFB9C8A5 (association_id), INDEX IDX_A427B9834584665A (product_id), PRIMARY KEY(association_id, product_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_association_type (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_6237029277153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_product_association ADD CONSTRAINT FK_48E9CDAB4584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_association ADD CONSTRAINT FK_48E9CDABB1E1C39 FOREIGN KEY (association_type_id) REFERENCES sylius_association_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_association_product ADD CONSTRAINT FK_A427B983EFB9C8A5 FOREIGN KEY (association_id) REFERENCES sylius_product_association (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_association_product ADD CONSTRAINT FK_A427B9834584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product_association_product DROP FOREIGN KEY FK_A427B983EFB9C8A5');
        $this->addSql('ALTER TABLE sylius_product_association DROP FOREIGN KEY FK_48E9CDABB1E1C39');
        $this->addSql('DROP TABLE sylius_product_association');
        $this->addSql('DROP TABLE sylius_product_association_product');
        $this->addSql('DROP TABLE sylius_association_type');
    }
}
