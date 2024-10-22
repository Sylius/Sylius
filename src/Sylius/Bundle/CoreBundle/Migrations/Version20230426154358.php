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

final class Version20230426154358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add ON DELETE SET NULL to main_taxon_id';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product DROP FOREIGN KEY FK_677B9B74731E505');
        $this->addSql('ALTER TABLE sylius_product ADD CONSTRAINT FK_677B9B74731E505 FOREIGN KEY (main_taxon_id) REFERENCES sylius_taxon (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product DROP FOREIGN KEY FK_677B9B74731E505');
        $this->addSql('ALTER TABLE sylius_product ADD CONSTRAINT FK_677B9B74731E505 FOREIGN KEY (main_taxon_id) REFERENCES sylius_taxon (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
