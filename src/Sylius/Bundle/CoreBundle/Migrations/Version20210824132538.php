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

final class Version20210824132538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add catalog promotion rule with relation to catalog promotion';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE sylius_catalog_promotion_rule (id INT AUTO_INCREMENT NOT NULL, promotion_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, configuration LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_CB6904F1139DF194 (promotion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_catalog_promotion_rule ADD CONSTRAINT FK_CB6904F1139DF194 FOREIGN KEY (promotion_id) REFERENCES sylius_catalog_promotion (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE sylius_catalog_promotion_rule');
    }
}
