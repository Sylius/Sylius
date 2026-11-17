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
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractPostgreSQLMigration;

final class Version20261016120001 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Remove Doctrine type comments, no longer used with DBAL 4 and disabled on DBAL 3 (PostgreSQL).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('COMMENT ON COLUMN sylius_payment_request.hash IS NULL');
        $this->addSql('COMMENT ON COLUMN sylius_payment_request.payload IS NULL');
        $this->addSql('COMMENT ON COLUMN sylius_payment_security_token.details IS NULL');

        if ($schema->hasTable('messenger_messages')) {
            $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS NULL');
            $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS NULL');
            $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS NULL');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('COMMENT ON COLUMN sylius_payment_request.hash IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN sylius_payment_request.payload IS \'(DC2Type:object)\'');
        $this->addSql('COMMENT ON COLUMN sylius_payment_security_token.details IS \'(DC2Type:object)\'');

        if ($schema->hasTable('messenger_messages')) {
            $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
            $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
            $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        }
    }
}
