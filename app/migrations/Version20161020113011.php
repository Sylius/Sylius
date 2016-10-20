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

final class Version20161020113011 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE sylius_customer DROP FOREIGN KEY FK_7E82D5E64D4CFF2B');
        $this->addSql('ALTER TABLE sylius_customer DROP FOREIGN KEY FK_7E82D5E679D0C0E4');
        $this->addSql('DROP INDEX UNIQ_7E82D5E679D0C0E4 ON sylius_customer');
        $this->addSql('DROP INDEX UNIQ_7E82D5E64D4CFF2B ON sylius_customer');
        $this->addSql('ALTER TABLE sylius_customer ADD default_address_id INT DEFAULT NULL, DROP shipping_address_id, DROP billing_address_id');
        $this->addSql('ALTER TABLE sylius_customer ADD CONSTRAINT FK_7E82D5E6BD94FB16 FOREIGN KEY (default_address_id) REFERENCES sylius_address (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7E82D5E6BD94FB16 ON sylius_customer (default_address_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE sylius_customer DROP FOREIGN KEY FK_7E82D5E6BD94FB16');
        $this->addSql('DROP INDEX UNIQ_7E82D5E6BD94FB16 ON sylius_customer');
        $this->addSql('ALTER TABLE sylius_customer ADD billing_address_id INT DEFAULT NULL, CHANGE default_address_id shipping_address_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_customer ADD CONSTRAINT FK_7E82D5E64D4CFF2B FOREIGN KEY (shipping_address_id) REFERENCES sylius_address (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE sylius_customer ADD CONSTRAINT FK_7E82D5E679D0C0E4 FOREIGN KEY (billing_address_id) REFERENCES sylius_address (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7E82D5E679D0C0E4 ON sylius_customer (billing_address_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7E82D5E64D4CFF2B ON sylius_customer (shipping_address_id)');
    }
}
