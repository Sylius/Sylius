<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160804091313 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_admin_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) DEFAULT NULL, username_canonical VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, password_reset_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, email_verification_token VARCHAR(255) DEFAULT NULL, verified_at DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, credentials_expire_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_api_access_token DROP FOREIGN KEY FK_7D83AA7FA76ED395');
        $this->addSql('ALTER TABLE sylius_api_access_token ADD CONSTRAINT FK_7D83AA7FA76ED395 FOREIGN KEY (user_id) REFERENCES sylius_admin_user (id)');
        $this->addSql('ALTER TABLE sylius_api_auth_code DROP FOREIGN KEY FK_C8404179A76ED395');
        $this->addSql('ALTER TABLE sylius_api_auth_code ADD CONSTRAINT FK_C8404179A76ED395 FOREIGN KEY (user_id) REFERENCES sylius_admin_user (id)');
        $this->addSql('ALTER TABLE sylius_api_refresh_token DROP FOREIGN KEY FK_44578525A76ED395');
        $this->addSql('ALTER TABLE sylius_api_refresh_token ADD CONSTRAINT FK_44578525A76ED395 FOREIGN KEY (user_id) REFERENCES sylius_admin_user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_api_access_token DROP FOREIGN KEY FK_7D83AA7FA76ED395');
        $this->addSql('ALTER TABLE sylius_api_auth_code DROP FOREIGN KEY FK_C8404179A76ED395');
        $this->addSql('ALTER TABLE sylius_api_refresh_token DROP FOREIGN KEY FK_44578525A76ED395');
        $this->addSql('DROP TABLE sylius_admin_user');
        $this->addSql('ALTER TABLE sylius_api_access_token DROP FOREIGN KEY FK_7D83AA7FA76ED395');
        $this->addSql('ALTER TABLE sylius_api_access_token ADD CONSTRAINT FK_7D83AA7FA76ED395 FOREIGN KEY (user_id) REFERENCES sylius_shop_user (id)');
        $this->addSql('ALTER TABLE sylius_api_auth_code DROP FOREIGN KEY FK_C8404179A76ED395');
        $this->addSql('ALTER TABLE sylius_api_auth_code ADD CONSTRAINT FK_C8404179A76ED395 FOREIGN KEY (user_id) REFERENCES sylius_shop_user (id)');
        $this->addSql('ALTER TABLE sylius_api_refresh_token DROP FOREIGN KEY FK_44578525A76ED395');
        $this->addSql('ALTER TABLE sylius_api_refresh_token ADD CONSTRAINT FK_44578525A76ED395 FOREIGN KEY (user_id) REFERENCES sylius_shop_user (id)');
    }
}
