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

final class Version20231106190918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create payment request';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE sylius_payment_request (hash BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', method_id INT DEFAULT NULL, payment_id INT DEFAULT NULL, state VARCHAR(255) NOT NULL, action VARCHAR(255) NOT NULL, payload LONGTEXT NOT NULL COMMENT \'(DC2Type:object)\', response_data JSON NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_86D904B19883967 (method_id), INDEX IDX_86D904B4C3A3BB (payment_id), PRIMARY KEY(hash)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_payment_request ADD CONSTRAINT FK_86D904B19883967 FOREIGN KEY (method_id) REFERENCES sylius_payment_method (id)');
        $this->addSql('ALTER TABLE sylius_payment_request ADD CONSTRAINT FK_86D904B4C3A3BB FOREIGN KEY (payment_id) REFERENCES sylius_payment (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_payment_request DROP FOREIGN KEY FK_86D904B19883967');
        $this->addSql('ALTER TABLE sylius_payment_request DROP FOREIGN KEY FK_86D904B4C3A3BB');
        $this->addSql('DROP TABLE sylius_payment_request');
    }
}
