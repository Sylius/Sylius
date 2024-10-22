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

final class Version20230426153930 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Add ON DELETE SET NULL to main_taxon_id';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product DROP CONSTRAINT FK_677B9B74731E505');
        $this->addSql('ALTER TABLE sylius_product ADD CONSTRAINT FK_677B9B74731E505 FOREIGN KEY (main_taxon_id) REFERENCES sylius_taxon (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product DROP CONSTRAINT fk_677b9b74731e505');
        $this->addSql('ALTER TABLE sylius_product ADD CONSTRAINT fk_677b9b74731e505 FOREIGN KEY (main_taxon_id) REFERENCES sylius_taxon (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
