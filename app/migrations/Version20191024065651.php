<?php

declare(strict_types=1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

final class Version20191024065651 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE sylius_shipping_method_rule (id INT AUTO_INCREMENT NOT NULL, shipping_method_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, configuration LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_88A0EB655F7D6850 (shipping_method_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_shipping_method_rule ADD CONSTRAINT FK_88A0EB655F7D6850 FOREIGN KEY (shipping_method_id) REFERENCES sylius_shipping_method (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE sylius_shipping_method_rule');
    }
}
