<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151002170842 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_promotion_benefit (id INT AUTO_INCREMENT NOT NULL, action_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, configuration LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_2F83E7199D32F035 (action_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_promotion_filter (id INT AUTO_INCREMENT NOT NULL, action_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, configuration LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_AB35DA9A9D32F035 (action_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_promotion_benefit ADD CONSTRAINT FK_2F83E7199D32F035 FOREIGN KEY (action_id) REFERENCES sylius_promotion_action (id)');
        $this->addSql('ALTER TABLE sylius_promotion_filter ADD CONSTRAINT FK_AB35DA9A9D32F035 FOREIGN KEY (action_id) REFERENCES sylius_promotion_action (id)');
        $this->addSql('ALTER TABLE sylius_promotion_action DROP type, DROP configuration');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sylius_promotion_benefit');
        $this->addSql('DROP TABLE sylius_promotion_filter');
        $this->addSql('ALTER TABLE sylius_promotion_action ADD type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD configuration LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\'');
        $this->addSql('CREATE FULLTEXT INDEX fulltext_search_idx ON sylius_search_index (value)');
    }
}
