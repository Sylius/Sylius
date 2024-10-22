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

final class Version20220203115813 extends AbstractMigration
{
    private const OLD_TRANSPORT_DSN = 'MESSENGER_TRANSPORT_DSN';

    private const MAIN_DSN = 'SYLIUS_MESSENGER_TRANSPORT_MAIN_DSN';

    private const MAIN_FAILED_DSN = 'SYLIUS_MESSENGER_TRANSPORT_MAIN_FAILED_DSN';

    private const CATALOG_PROMOTION_REMOVAL_DSN = 'SYLIUS_MESSENGER_TRANSPORT_CATALOG_PROMOTION_REMOVAL_DSN';

    private const CATALOG_PROMOTION_REMOVAL_FAILED_DSN = 'SYLIUS_MESSENGER_TRANSPORT_CATALOG_PROMOTION_REMOVAL_FAILED_DSN';

    public function getDescription(): string
    {
        return 'Add messenger transport table for doctrine transports.';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->isUsingDoctrineTransport(), 'No doctrine transport found.');

        if (!$schema->hasTable('messenger_messages')) {
            $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        }
    }

    public function down(Schema $schema): void
    {
    }

    private function isUsingDoctrineTransport(): bool
    {
        return $this->isUsingLegacyTransport() || $this->isUsingSupportedTransport();
    }

    private function isUsingLegacyTransport(): bool
    {
        return array_key_exists(self::OLD_TRANSPORT_DSN, $_ENV) && str_contains($_ENV[self::OLD_TRANSPORT_DSN], 'doctrine');
    }

    private function isUsingSupportedTransport(): bool
    {
        return
            array_key_exists(self::MAIN_DSN, $_ENV) && str_contains($_ENV[self::MAIN_DSN], 'doctrine') ||
            array_key_exists(self::MAIN_FAILED_DSN, $_ENV) && str_contains($_ENV[self::MAIN_FAILED_DSN], 'doctrine') ||
            array_key_exists(self::CATALOG_PROMOTION_REMOVAL_DSN, $_ENV) && str_contains($_ENV[self::CATALOG_PROMOTION_REMOVAL_DSN], 'doctrine') ||
            array_key_exists(self::CATALOG_PROMOTION_REMOVAL_FAILED_DSN, $_ENV) && str_contains($_ENV[self::CATALOG_PROMOTION_REMOVAL_FAILED_DSN], 'doctrine')
        ;
    }
}
