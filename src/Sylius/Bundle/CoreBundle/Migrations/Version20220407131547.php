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

final class Version20220407131547 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update messenger transport table: change queue_name length and add two indexes.';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$schema->hasTable('messenger_messages'), 'messenger_messages table was not found');

        $existingIndexes = $this->getExistingIndexesNames('messenger_messages');

        $this->addSql('ALTER TABLE messenger_messages CHANGE queue_name queue_name VARCHAR(190) NOT NULL');

        if (!in_array('IDX_75EA56E0FB7336F0', $existingIndexes)) {
            $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        }

        if (!in_array('IDX_75EA56E0E3BD61CE', $existingIndexes)) {
            $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        }
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('messenger_messages')) {
            $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0 ON messenger_messages');
            $this->addSql('DROP INDEX IDX_75EA56E0E3BD61CE ON messenger_messages');
            $this->addSql('ALTER TABLE messenger_messages CHANGE queue_name queue_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        }
    }

    private function getExistingIndexesNames(string $tableName): array
    {
        $indexes = $this->connection->createSchemaManager()->listTableIndexes($tableName);

        $indexesNames = [];

        foreach ($indexes as $index) {
            $indexesNames[] = $index->getName();
        }

        return $indexesNames;
    }
}
