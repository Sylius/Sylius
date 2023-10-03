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

final class Version20220926113252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create promotion translation';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE sylius_promotion_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, label VARCHAR(255) DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_3C7A76182C2AC5D3 (translatable_id), UNIQUE INDEX sylius_promotion_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_promotion_translation ADD CONSTRAINT FK_3C7A76182C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES sylius_promotion (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_promotion_translation DROP FOREIGN KEY FK_3C7A76182C2AC5D3');
        $this->addSql('DROP TABLE sylius_promotion_translation');
    }
}
