<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160725084855 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_channel DROP FOREIGN KEY FK_16C8119EECD792C0');
        $this->addSql('ALTER TABLE sylius_channel CHANGE default_currency_id default_currency_id INT NOT NULL');
        $this->addSql('ALTER TABLE sylius_channel ADD CONSTRAINT FK_16C8119EECD792C0 FOREIGN KEY (default_currency_id) REFERENCES sylius_currency (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_channel DROP FOREIGN KEY FK_16C8119EECD792C0');
        $this->addSql('ALTER TABLE sylius_channel CHANGE default_currency_id default_currency_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_channel ADD CONSTRAINT FK_16C8119EECD792C0 FOREIGN KEY (default_currency_id) REFERENCES sylius_currency (id) ON DELETE SET NULL');
    }
}
