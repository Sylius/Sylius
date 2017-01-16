<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170116215417 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_payment_method ADD gateway_config_id INT DEFAULT NULL, DROP gateway');
        $this->addSql('ALTER TABLE sylius_payment_method ADD CONSTRAINT FK_A75B0B0DF23D6140 FOREIGN KEY (gateway_config_id) REFERENCES sylius_gateway_config (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_A75B0B0DF23D6140 ON sylius_payment_method (gateway_config_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_payment_method DROP FOREIGN KEY FK_A75B0B0DF23D6140');
        $this->addSql('DROP INDEX IDX_A75B0B0DF23D6140 ON sylius_payment_method');
        $this->addSql('ALTER TABLE sylius_payment_method ADD gateway VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP gateway_config_id');
    }
}
