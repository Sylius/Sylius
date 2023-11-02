<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231103004216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Making the uniqueness of the password_reset_token and email_verification_token a database constraint';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88D5CC4D6B7BA4B6 ON sylius_admin_user (password_reset_token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88D5CC4DC4995C67 ON sylius_admin_user (email_verification_token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7C2B74806B7BA4B6 ON sylius_shop_user (password_reset_token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7C2B7480C4995C67 ON sylius_shop_user (email_verification_token)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_88D5CC4D6B7BA4B6 ON sylius_admin_user');
        $this->addSql('DROP INDEX UNIQ_88D5CC4DC4995C67 ON sylius_admin_user');
        $this->addSql('DROP INDEX UNIQ_7C2B74806B7BA4B6 ON sylius_shop_user');
        $this->addSql('DROP INDEX UNIQ_7C2B7480C4995C67 ON sylius_shop_user');
    }
}
