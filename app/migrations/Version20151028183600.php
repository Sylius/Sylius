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
class Version20151028183600 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE sylius_payment_security_token CHANGE payment_name gateway_name VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';");
        $this->addSql("ALTER TABLE sylius_payment_config CHANGE payment_name gateway_name VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';");
        $this->addSql('RENAME TABLE `sylius_payment_config` TO `sylius_gateway_config`;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE payment_name gateway_name CHANGE sylius_payment_security_token VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';");
        $this->addSql("ALTER TABLE payment_name gateway_name CHANGE sylius_payment_config VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';");
        $this->addSql('RENAME TABLE `sylius_gateway_config` TO `sylius_payment_config`;');
    }
}
