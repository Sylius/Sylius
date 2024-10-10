<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

final class Version20241010135921 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add position property to the Zone';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_zone ADD position INT NOT NULL DEFAULT 0');
        $this->addSql('UPDATE sylius_zone SET position = id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_zone DROP position');
    }
}
