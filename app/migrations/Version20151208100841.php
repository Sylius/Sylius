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
class Version20151208100841 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_channel ADD default_locale_id INT DEFAULT NULL, ADD default_currency_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_channel ADD CONSTRAINT FK_16C8119E743BF776 FOREIGN KEY (default_locale_id) REFERENCES sylius_locale (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE sylius_channel ADD CONSTRAINT FK_16C8119EECD792C0 FOREIGN KEY (default_currency_id) REFERENCES sylius_currency (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_16C8119E743BF776 ON sylius_channel (default_locale_id)');
        $this->addSql('CREATE INDEX IDX_16C8119EECD792C0 ON sylius_channel (default_currency_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_channel DROP FOREIGN KEY FK_16C8119E743BF776');
        $this->addSql('ALTER TABLE sylius_channel DROP FOREIGN KEY FK_16C8119EECD792C0');
        $this->addSql('DROP INDEX IDX_16C8119E743BF776 ON sylius_channel');
        $this->addSql('DROP INDEX IDX_16C8119EECD792C0 ON sylius_channel');
        $this->addSql('ALTER TABLE sylius_channel DROP default_locale_id, DROP default_currency_id');
    }
}
