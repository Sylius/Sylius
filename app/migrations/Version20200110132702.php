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

namespace Sylius\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

final class Version20200110132702 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sylius_channel ADD menu_taxon_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_channel ADD CONSTRAINT FK_16C8119EF242B1E6 FOREIGN KEY (menu_taxon_id) REFERENCES sylius_taxon (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_16C8119EF242B1E6 ON sylius_channel (menu_taxon_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sylius_channel DROP FOREIGN KEY FK_16C8119EF242B1E6');
        $this->addSql('DROP INDEX IDX_16C8119EF242B1E6 ON sylius_channel');
        $this->addSql('ALTER TABLE sylius_channel DROP menu_taxon_id');
    }
}
