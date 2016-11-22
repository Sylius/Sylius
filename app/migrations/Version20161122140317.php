<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161122140317 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_exchange_rate (id INT AUTO_INCREMENT NOT NULL, source_currency INT NOT NULL, target_currency INT NOT NULL, ratio NUMERIC(10, 5) NOT NULL, INDEX IDX_5F52B852A76BEED (source_currency), INDEX IDX_5F52B85B3FD5856 (target_currency), UNIQUE INDEX UNIQ_5F52B852A76BEEDB3FD5856 (source_currency, target_currency), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_exchange_rate ADD CONSTRAINT FK_5F52B852A76BEED FOREIGN KEY (source_currency) REFERENCES sylius_currency (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_exchange_rate ADD CONSTRAINT FK_5F52B85B3FD5856 FOREIGN KEY (target_currency) REFERENCES sylius_currency (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sylius_exchange_rate');
    }
}
