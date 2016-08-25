<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160819080651 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_contact_request DROP FOREIGN KEY FK_8B0BBF201F55203D');
        $this->addSql('ALTER TABLE sylius_contact_topic_translation DROP FOREIGN KEY FK_6681216F2C2AC5D3');
        $this->addSql('DROP TABLE sylius_contact_request');
        $this->addSql('DROP TABLE sylius_contact_topic');
        $this->addSql('DROP TABLE sylius_contact_topic_translation');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_contact_request (id INT AUTO_INCREMENT NOT NULL, topic_id INT DEFAULT NULL, first_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, last_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, email VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, message LONGTEXT NOT NULL COLLATE utf8_unicode_ci, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_8B0BBF201F55203D (topic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_contact_topic (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_contact_topic_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, locale VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX sylius_contact_topic_translation_uniq_trans (translatable_id, locale), INDEX IDX_6681216F2C2AC5D3 (translatable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_contact_request ADD CONSTRAINT FK_8B0BBF201F55203D FOREIGN KEY (topic_id) REFERENCES sylius_contact_topic (id)');
        $this->addSql('ALTER TABLE sylius_contact_topic_translation ADD CONSTRAINT FK_6681216F2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES sylius_contact_topic (id) ON DELETE CASCADE');
    }
}
