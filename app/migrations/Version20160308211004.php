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
class Version20160308211004 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_channel_taxonomy DROP FOREIGN KEY FK_4BE9652E9557E6F6');
        $this->addSql('ALTER TABLE sylius_taxon DROP FOREIGN KEY FK_CFD811CA9557E6F6');
        $this->addSql('ALTER TABLE sylius_taxonomy_translation DROP FOREIGN KEY FK_9F3F90D92C2AC5D3');
        $this->addSql('CREATE TABLE sylius_channel_taxon (channel_id INT NOT NULL, taxon_id INT NOT NULL, INDEX IDX_E11F1D72F5A1AA (channel_id), INDEX IDX_E11F1DDE13F470 (taxon_id), PRIMARY KEY(channel_id, taxon_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_channel_taxon ADD CONSTRAINT FK_E11F1D72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_taxon ADD CONSTRAINT FK_E11F1DDE13F470 FOREIGN KEY (taxon_id) REFERENCES sylius_taxon (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE sylius_channel_taxonomy');
        $this->addSql('DROP TABLE sylius_taxonomy');
        $this->addSql('DROP TABLE sylius_taxonomy_translation');
        $this->addSql('DROP INDEX IDX_CFD811CA9557E6F6 ON sylius_taxon');
        $this->addSql('ALTER TABLE sylius_taxon DROP deleted_at, CHANGE taxonomy_id tree_root INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_taxon ADD CONSTRAINT FK_CFD811CAA977936C FOREIGN KEY (tree_root) REFERENCES sylius_taxon (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_CFD811CAA977936C ON sylius_taxon (tree_root)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_channel_taxonomy (channel_id INT NOT NULL, taxonomy_id INT NOT NULL, INDEX IDX_4BE9652E72F5A1AA (channel_id), INDEX IDX_4BE9652E9557E6F6 (taxonomy_id), PRIMARY KEY(channel_id, taxonomy_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_taxonomy (id INT AUTO_INCREMENT NOT NULL, root_id INT DEFAULT NULL, INDEX IDX_2A9E3D279066886 (root_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_taxonomy_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, locale VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX sylius_taxonomy_translation_uniq_trans (translatable_id, locale), INDEX IDX_9F3F90D92C2AC5D3 (translatable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_channel_taxonomy ADD CONSTRAINT FK_4BE9652E72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_taxonomy ADD CONSTRAINT FK_4BE9652E9557E6F6 FOREIGN KEY (taxonomy_id) REFERENCES sylius_taxonomy (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_taxonomy ADD CONSTRAINT FK_2A9E3D279066886 FOREIGN KEY (root_id) REFERENCES sylius_taxon (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE sylius_taxonomy_translation ADD CONSTRAINT FK_9F3F90D92C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES sylius_taxonomy (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE sylius_channel_taxon');
        $this->addSql('ALTER TABLE sylius_taxon DROP FOREIGN KEY FK_CFD811CAA977936C');
        $this->addSql('DROP INDEX IDX_CFD811CAA977936C ON sylius_taxon');
        $this->addSql('ALTER TABLE sylius_taxon ADD deleted_at DATETIME DEFAULT NULL, CHANGE tree_root taxonomy_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_taxon ADD CONSTRAINT FK_CFD811CA9557E6F6 FOREIGN KEY (taxonomy_id) REFERENCES sylius_taxonomy (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_CFD811CA9557E6F6 ON sylius_taxon (taxonomy_id)');
    }
}
