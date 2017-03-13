<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170313125424 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_api_access_token RENAME sylius_admin_api_access_token');
        $this->addSql('ALTER TABLE sylius_api_auth_code RENAME sylius_admin_api_auth_code');
        $this->addSql('ALTER TABLE sylius_api_client RENAME sylius_admin_api_client');
        $this->addSql('ALTER TABLE sylius_api_refresh_token RENAME sylius_admin_api_refresh_token');
        $this->addSql('ALTER TABLE sylius_admin_api_access_token RENAME INDEX uniq_7d83aa7f5f37a13b TO UNIQ_2AA4915D5F37A13B');
        $this->addSql('ALTER TABLE sylius_admin_api_access_token RENAME INDEX idx_7d83aa7f19eb6921 TO IDX_2AA4915D19EB6921');
        $this->addSql('ALTER TABLE sylius_admin_api_access_token RENAME INDEX idx_7d83aa7fa76ed395 TO IDX_2AA4915DA76ED395');
        $this->addSql('ALTER TABLE sylius_admin_api_auth_code RENAME INDEX uniq_c84041795f37a13b TO UNIQ_E366D8485F37A13B');
        $this->addSql('ALTER TABLE sylius_admin_api_auth_code RENAME INDEX idx_c840417919eb6921 TO IDX_E366D84819EB6921');
        $this->addSql('ALTER TABLE sylius_admin_api_auth_code RENAME INDEX idx_c8404179a76ed395 TO IDX_E366D848A76ED395');
        $this->addSql('ALTER TABLE sylius_admin_api_refresh_token RENAME INDEX uniq_445785255f37a13b TO UNIQ_9160E3FA5F37A13B');
        $this->addSql('ALTER TABLE sylius_admin_api_refresh_token RENAME INDEX idx_4457852519eb6921 TO IDX_9160E3FA19EB6921');
        $this->addSql('ALTER TABLE sylius_admin_api_refresh_token RENAME INDEX idx_44578525a76ed395 TO IDX_9160E3FAA76ED395');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_admin_api_access_token RENAME INDEX uniq_2aa4915d5f37a13b TO UNIQ_7D83AA7F5F37A13B');
        $this->addSql('ALTER TABLE sylius_admin_api_access_token RENAME INDEX idx_2aa4915d19eb6921 TO IDX_7D83AA7F19EB6921');
        $this->addSql('ALTER TABLE sylius_admin_api_access_token RENAME INDEX idx_2aa4915da76ed395 TO IDX_7D83AA7FA76ED395');
        $this->addSql('ALTER TABLE sylius_admin_api_auth_code RENAME INDEX uniq_e366d8485f37a13b TO UNIQ_C84041795F37A13B');
        $this->addSql('ALTER TABLE sylius_admin_api_auth_code RENAME INDEX idx_e366d84819eb6921 TO IDX_C840417919EB6921');
        $this->addSql('ALTER TABLE sylius_admin_api_auth_code RENAME INDEX idx_e366d848a76ed395 TO IDX_C8404179A76ED395');
        $this->addSql('ALTER TABLE sylius_admin_api_refresh_token RENAME INDEX uniq_9160e3fa5f37a13b TO UNIQ_445785255F37A13B');
        $this->addSql('ALTER TABLE sylius_admin_api_refresh_token RENAME INDEX idx_9160e3fa19eb6921 TO IDX_4457852519EB6921');
        $this->addSql('ALTER TABLE sylius_admin_api_refresh_token RENAME INDEX idx_9160e3faa76ed395 TO IDX_44578525A76ED395');
        $this->addSql('ALTER TABLE sylius_admin_api_access_token RENAME sylius_api_access_token');
        $this->addSql('ALTER TABLE sylius_admin_api_auth_code RENAME sylius_api_auth_code');
        $this->addSql('ALTER TABLE sylius_admin_api_client RENAME sylius_api_client');
        $this->addSql('ALTER TABLE sylius_admin_api_refresh_token RENAME sylius_api_refresh_token');
    }
}
