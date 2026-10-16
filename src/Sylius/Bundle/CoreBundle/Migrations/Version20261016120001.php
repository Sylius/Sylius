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
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractPostgreSQLMigration;

final class Version20261016120001 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Align Doctrine type comments with the installed DBAL version (PostgreSQL).';
    }

    public function up(Schema $schema): void
    {
        // The schema is altered through the Schema object, so DBAL itself decides whether type comments are needed:
        // DBAL 3 adds the missing "(DC2Type:datetime_immutable)" comment, DBAL 4 drops the leftover "(DC2Type:...)" ones.
        $schema->getTable('sylius_promotion_coupon')->getColumn('track_usage_since')
            ->setType(Type::getType(Types::DATETIME_IMMUTABLE))
            ->setComment('');

        $paymentRequestTable = $schema->getTable('sylius_payment_request');
        $paymentRequestTable->getColumn('hash')->setComment('');
        $paymentRequestTable->getColumn('payload')->setComment('');

        $schema->getTable('sylius_payment_security_token')->getColumn('details')->setComment('');
    }

    public function down(Schema $schema): void
    {
    }
}
