<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161115142753 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_payment_method_channels (payment_method_id INT NOT NULL, channel_id INT NOT NULL, INDEX IDX_543AC0CC5AA1164F (payment_method_id), INDEX IDX_543AC0CC72F5A1AA (channel_id), PRIMARY KEY(payment_method_id, channel_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_shipping_method_channels (shipping_method_id INT NOT NULL, channel_id INT NOT NULL, INDEX IDX_2D9833355F7D6850 (shipping_method_id), INDEX IDX_2D98333572F5A1AA (channel_id), PRIMARY KEY(shipping_method_id, channel_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_payment_method_channels ADD CONSTRAINT FK_543AC0CC5AA1164F FOREIGN KEY (payment_method_id) REFERENCES sylius_payment_method (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_payment_method_channels ADD CONSTRAINT FK_543AC0CC72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_shipping_method_channels ADD CONSTRAINT FK_2D9833355F7D6850 FOREIGN KEY (shipping_method_id) REFERENCES sylius_shipping_method (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_shipping_method_channels ADD CONSTRAINT FK_2D98333572F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE sylius_channel_payment_methods');
        $this->addSql('DROP TABLE sylius_channel_shipping_methods');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_channel_payment_methods (channel_id INT NOT NULL, payment_method_id INT NOT NULL, INDEX IDX_B0C0002B72F5A1AA (channel_id), INDEX IDX_B0C0002B5AA1164F (payment_method_id), PRIMARY KEY(channel_id, payment_method_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_channel_shipping_methods (channel_id INT NOT NULL, shipping_method_id INT NOT NULL, INDEX IDX_6858B18E72F5A1AA (channel_id), INDEX IDX_6858B18E5F7D6850 (shipping_method_id), PRIMARY KEY(channel_id, shipping_method_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_channel_payment_methods ADD CONSTRAINT FK_B0C0002B5AA1164F FOREIGN KEY (payment_method_id) REFERENCES sylius_payment_method (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_payment_methods ADD CONSTRAINT FK_B0C0002B72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_shipping_methods ADD CONSTRAINT FK_6858B18E5F7D6850 FOREIGN KEY (shipping_method_id) REFERENCES sylius_shipping_method (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel_shipping_methods ADD CONSTRAINT FK_6858B18E72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE sylius_payment_method_channels');
        $this->addSql('DROP TABLE sylius_shipping_method_channels');
    }
}
