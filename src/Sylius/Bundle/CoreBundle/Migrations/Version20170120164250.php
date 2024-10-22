<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

class Version20170120164250 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_payment DROP FOREIGN KEY FK_D9191BD47048FD0F');
        $this->addSql('DROP TABLE sylius_credit_card');
        $this->addSql('DROP INDEX IDX_D9191BD47048FD0F ON sylius_payment');
        $this->addSql('ALTER TABLE sylius_payment DROP credit_card_id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE sylius_credit_card (id INT AUTO_INCREMENT NOT NULL, token VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, type VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, cardholder_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, number VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, security_code VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, expiry_month INT DEFAULT NULL, expiry_year INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_payment ADD credit_card_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_payment ADD CONSTRAINT FK_D9191BD47048FD0F FOREIGN KEY (credit_card_id) REFERENCES sylius_credit_card (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_D9191BD47048FD0F ON sylius_payment (credit_card_id)');
    }
}
