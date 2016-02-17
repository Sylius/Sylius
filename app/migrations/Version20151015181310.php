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

class Version20151015181310 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $paymentMethods = $this->connection->fetchAll('SELECT * FROM sylius_payment_method WHERE fee_calculator_configuration=""');
        foreach ($paymentMethods as $paymentMethod) {
            $this->connection->update(
                'sylius_payment_method',
                ['fee_calculator' => 'fixed', 'fee_calculator_configuration' => serialize(['amount' => 0])],
                ['name' => $paymentMethod['name']]
            );
        }
    }

    public function down(Schema $schema)
    {
    }
}
