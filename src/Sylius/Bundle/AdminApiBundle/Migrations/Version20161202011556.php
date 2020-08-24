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

namespace Sylius\Bundle\AdminApiBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20161202011556 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        /**
         * How this table could exist if not created by this migration before?
         * Migration Version20161202011555 has been split between CoreBundle and AdminApiBundle.
         *
         * CoreBundle's one uses the same class name (Sylius\Bundle\CoreBundle\Migrations\Version20161202011555), but
         * has all AdminApiBundle's queries removed.
         *
         * AdminApiBundle's one uses a different name (version number incremented by one, Sylius\Bundle\AdminApiBundle\Migrations\Version20161202011556),
         * but it makes Doctrine Migrations run this migration after upgrading to Migrations v3.
         *
         * By performing this check, we can discover whether this migration is run just after the upgrade and skip it instead.
         */
        if ($schema->hasTable('sylius_api_client')) {
            return;
        }

        $this->addSql('CREATE TABLE sylius_api_access_token (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_7D83AA7F5F37A13B (token), INDEX IDX_7D83AA7F19EB6921 (client_id), INDEX IDX_7D83AA7FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_api_auth_code (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, redirect_uri LONGTEXT NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_C84041795F37A13B (token), INDEX IDX_C840417919EB6921 (client_id), INDEX IDX_C8404179A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_api_client (id INT AUTO_INCREMENT NOT NULL, random_id VARCHAR(255) NOT NULL, redirect_uris LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', secret VARCHAR(255) NOT NULL, allowed_grant_types LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_api_refresh_token (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_445785255F37A13B (token), INDEX IDX_4457852519EB6921 (client_id), INDEX IDX_44578525A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_api_access_token ADD CONSTRAINT FK_7D83AA7F19EB6921 FOREIGN KEY (client_id) REFERENCES sylius_api_client (id)');
        $this->addSql('ALTER TABLE sylius_api_access_token ADD CONSTRAINT FK_7D83AA7FA76ED395 FOREIGN KEY (user_id) REFERENCES sylius_admin_user (id)');
        $this->addSql('ALTER TABLE sylius_api_auth_code ADD CONSTRAINT FK_C840417919EB6921 FOREIGN KEY (client_id) REFERENCES sylius_api_client (id)');
        $this->addSql('ALTER TABLE sylius_api_auth_code ADD CONSTRAINT FK_C8404179A76ED395 FOREIGN KEY (user_id) REFERENCES sylius_admin_user (id)');
        $this->addSql('ALTER TABLE sylius_api_refresh_token ADD CONSTRAINT FK_4457852519EB6921 FOREIGN KEY (client_id) REFERENCES sylius_api_client (id)');
        $this->addSql('ALTER TABLE sylius_api_refresh_token ADD CONSTRAINT FK_44578525A76ED395 FOREIGN KEY (user_id) REFERENCES sylius_admin_user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_api_access_token DROP FOREIGN KEY FK_7D83AA7FA76ED395');
        $this->addSql('ALTER TABLE sylius_api_auth_code DROP FOREIGN KEY FK_C8404179A76ED395');
        $this->addSql('ALTER TABLE sylius_api_refresh_token DROP FOREIGN KEY FK_44578525A76ED395');
        $this->addSql('ALTER TABLE sylius_api_access_token DROP FOREIGN KEY FK_7D83AA7F19EB6921');
        $this->addSql('ALTER TABLE sylius_api_auth_code DROP FOREIGN KEY FK_C840417919EB6921');
        $this->addSql('ALTER TABLE sylius_api_refresh_token DROP FOREIGN KEY FK_4457852519EB6921');
        $this->addSql('DROP TABLE sylius_api_access_token');
        $this->addSql('DROP TABLE sylius_api_auth_code');
        $this->addSql('DROP TABLE sylius_api_client');
        $this->addSql('DROP TABLE sylius_api_refresh_token');
    }
}
