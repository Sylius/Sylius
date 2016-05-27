<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160527050501 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_metadata_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, locale VARCHAR(255) NOT NULL, metadata LONGTEXT NOT NULL, INDEX IDX_CD04EB42C2AC5D3 (translatable_id), UNIQUE INDEX sylius_metadata_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_metadata_translation ADD CONSTRAINT FK_CD04EB42C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES sylius_metadata (id) ON DELETE CASCADE');

        // TODO: Should really get the default locale here...
        $this->addSql('INSERT INTO sylius_metadata_translation (translatable_id, metadata, locale) (SELECT id, metadata, "en_US" FROM sylius_metadata)');

        $this->addSql('ALTER TABLE sylius_metadata DROP metadata');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sylius_metadata_translation');
        $this->addSql('ALTER TABLE sylius_metadata ADD metadata LONGTEXT NOT NULL COLLATE utf8_unicode_ci');
    }
}
