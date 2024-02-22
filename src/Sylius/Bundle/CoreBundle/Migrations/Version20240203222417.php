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

final class Version20240203222417 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Create payment request';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE sylius_payment_request (hash UUID NOT NULL, method_id INT DEFAULT NULL, payment_id INT DEFAULT NULL, state VARCHAR(255) NOT NULL, action VARCHAR(255) NOT NULL, request_payload TEXT NOT NULL, response_data JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(hash))');
        $this->addSql('CREATE INDEX IDX_86D904B19883967 ON sylius_payment_request (method_id)');
        $this->addSql('CREATE INDEX IDX_86D904B4C3A3BB ON sylius_payment_request (payment_id)');
        $this->addSql('COMMENT ON COLUMN sylius_payment_request.hash IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN sylius_payment_request.request_payload IS \'(DC2Type:object)\'');
        $this->addSql('ALTER TABLE sylius_payment_request ADD CONSTRAINT FK_86D904B19883967 FOREIGN KEY (method_id) REFERENCES sylius_payment_method (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sylius_payment_request ADD CONSTRAINT FK_86D904B4C3A3BB FOREIGN KEY (payment_id) REFERENCES sylius_payment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_payment_request DROP CONSTRAINT FK_86D904B19883967');
        $this->addSql('ALTER TABLE sylius_payment_request DROP CONSTRAINT FK_86D904B4C3A3BB');
        $this->addSql('DROP TABLE sylius_payment_request');
    }
}
