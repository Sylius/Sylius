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

namespace Sylius\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

class Version20170116215417 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_payment_method ADD gateway_config_id INT DEFAULT NULL, DROP gateway');
        $this->addSql('ALTER TABLE sylius_payment_method ADD CONSTRAINT FK_A75B0B0DF23D6140 FOREIGN KEY (gateway_config_id) REFERENCES sylius_gateway_config (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_A75B0B0DF23D6140 ON sylius_payment_method (gateway_config_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_payment_method DROP FOREIGN KEY FK_A75B0B0DF23D6140');
        $this->addSql('DROP INDEX IDX_A75B0B0DF23D6140 ON sylius_payment_method');
        $this->addSql('ALTER TABLE sylius_payment_method ADD gateway VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP gateway_config_id');
    }
}
