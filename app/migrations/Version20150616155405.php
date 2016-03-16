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
class Version20150616155405 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_channel_taxonomy (channel_id INT NOT NULL, taxonomy_id INT NOT NULL, INDEX IDX_4BE9652E72F5A1AA (channel_id), INDEX IDX_4BE9652E9557E6F6 (taxonomy_id), PRIMARY KEY(channel_id, taxonomy_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_channel_taxonomy ADD CONSTRAINT FK_4BE9652E72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_taxonomy ADD CONSTRAINT FK_4BE9652E9557E6F6 FOREIGN KEY (taxonomy_id) REFERENCES sylius_taxonomy (id) ON DELETE CASCADE');
        $this->addSql('INSERT INTO sylius_channel_taxonomy (channel_id, taxonomy_id) SELECT channel_id, taxonomy_id FROM sylius_product_taxonomy');
        $this->addSql('DROP TABLE sylius_product_taxonomy');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_product_taxonomy (channel_id INT NOT NULL, taxonomy_id INT NOT NULL, INDEX IDX_F7E97C1072F5A1AA (channel_id), INDEX IDX_F7E97C109557E6F6 (taxonomy_id), PRIMARY KEY(channel_id, taxonomy_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_product_taxonomy ADD CONSTRAINT FK_F7E97C109557E6F6 FOREIGN KEY (taxonomy_id) REFERENCES sylius_taxonomy (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_taxonomy ADD CONSTRAINT FK_F7E97C1072F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('INSERT INTO sylius_product_taxonomy (channel_id, taxonomy_id) SELECT channel_id, taxonomy_id FROM sylius_channel_taxonomy');
        $this->addSql('DROP TABLE sylius_channel_taxonomy');
    }
}
