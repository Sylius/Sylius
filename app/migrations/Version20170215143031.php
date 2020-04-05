<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20170215143031 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_105A908989D9B62 ON sylius_product_translation');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_105A9084180C698989D9B62 ON sylius_product_translation (locale, slug)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_105A9084180C698989D9B62 ON sylius_product_translation');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_105A908989D9B62 ON sylius_product_translation (slug)');
    }
}
