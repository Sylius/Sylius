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

final class Version20261016120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove Doctrine type comments, no longer used with DBAL 4 and disabled on DBAL 3.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_payment_request CHANGE hash hash BINARY(16) NOT NULL, CHANGE payload payload LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE sylius_payment_security_token CHANGE details details LONGTEXT DEFAULT NULL');

        if ($schema->hasTable('messenger_messages')) {
            $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL, CHANGE available_at available_at DATETIME NOT NULL, CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_payment_request CHANGE hash hash BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE payload payload LONGTEXT NOT NULL COMMENT \'(DC2Type:object)\'');
        $this->addSql('ALTER TABLE sylius_payment_security_token CHANGE details details LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\'');

        if ($schema->hasTable('messenger_messages')) {
            $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE available_at available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        }
    }
}
