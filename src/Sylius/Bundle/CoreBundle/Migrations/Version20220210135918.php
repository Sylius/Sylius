<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220210135918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add guest field to mark orders made by guests';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_order ADD guest TINYINT(1) DEFAULT \'1\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_order DROP guest');
    }
}
