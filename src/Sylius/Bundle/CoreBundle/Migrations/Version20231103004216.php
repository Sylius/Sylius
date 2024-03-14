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
use Doctrine\Migrations\AbstractMigration;

final class Version20231103004216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add unique indices for password_reset_token, email_verification_token fields to sylius_shop_user and sylius_admin_user tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88D5CC4D6B7BA4B6 ON sylius_admin_user (password_reset_token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88D5CC4DC4995C67 ON sylius_admin_user (email_verification_token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7C2B74806B7BA4B6 ON sylius_shop_user (password_reset_token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7C2B7480C4995C67 ON sylius_shop_user (email_verification_token)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_88D5CC4D6B7BA4B6 ON sylius_admin_user');
        $this->addSql('DROP INDEX UNIQ_88D5CC4DC4995C67 ON sylius_admin_user');
        $this->addSql('DROP INDEX UNIQ_7C2B74806B7BA4B6 ON sylius_shop_user');
        $this->addSql('DROP INDEX UNIQ_7C2B7480C4995C67 ON sylius_shop_user');
    }
}
