<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161122122836 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_exchange_rate (id INT AUTO_INCREMENT NOT NULL, base_currency INT NOT NULL, counter_currency INT NOT NULL, ratio NUMERIC(10, 5) NOT NULL, INDEX IDX_5F52B8523BD2BC7 (base_currency), INDEX IDX_5F52B8525B1F33D (counter_currency), UNIQUE INDEX UNIQ_5F52B8523BD2BC725B1F33D (base_currency, counter_currency), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_exchange_rate ADD CONSTRAINT FK_5F52B8523BD2BC7 FOREIGN KEY (base_currency) REFERENCES sylius_currency (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_exchange_rate ADD CONSTRAINT FK_5F52B8525B1F33D FOREIGN KEY (counter_currency) REFERENCES sylius_currency (id) ON DELETE CASCADE');
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
