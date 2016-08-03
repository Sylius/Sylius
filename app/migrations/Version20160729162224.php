<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160729162224 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_channel DROP FOREIGN KEY FK_16C8119E743BF776');
        $this->addSql('ALTER TABLE sylius_channel CHANGE default_locale_id default_locale_id INT NOT NULL');
        $this->addSql('ALTER TABLE sylius_channel ADD CONSTRAINT FK_16C8119E743BF776 FOREIGN KEY (default_locale_id) REFERENCES sylius_locale (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_channel DROP FOREIGN KEY FK_16C8119E743BF776');
        $this->addSql('ALTER TABLE sylius_channel CHANGE default_locale_id default_locale_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_channel ADD CONSTRAINT FK_16C8119E743BF776 FOREIGN KEY (default_locale_id) REFERENCES sylius_locale (id) ON DELETE SET NULL');
    }
}
