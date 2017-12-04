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
        $this->addSql('ALTER TABLE sylius_admin_api_access_token DROP FOREIGN KEY FK_7D83AA7F19EB6921');
        $this->addSql('ALTER TABLE sylius_admin_api_access_token DROP FOREIGN KEY FK_7D83AA7FA76ED395');
        $this->addSql('DROP INDEX UNIQ_7D83AA7F5F37A13B ON sylius_admin_api_access_token');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2AA4915D5F37A13B ON sylius_admin_api_access_token (token)');
        $this->addSql('DROP INDEX IDX_7D83AA7F19EB6921 ON sylius_admin_api_access_token');
        $this->addSql('CREATE INDEX IDX_2AA4915D19EB6921 ON sylius_admin_api_access_token (client_id)');
        $this->addSql('DROP INDEX IDX_7D83AA7FA76ED395 ON sylius_admin_api_access_token');
        $this->addSql('CREATE INDEX IDX_2AA4915DA76ED395 ON sylius_admin_api_access_token (user_id)');
        $this->addSql('ALTER TABLE sylius_admin_api_access_token ADD CONSTRAINT FK_2AA4915D19EB6921 FOREIGN KEY (client_id) REFERENCES sylius_admin_api_client (id)');
        $this->addSql('ALTER TABLE sylius_admin_api_access_token ADD CONSTRAINT FK_2AA4915DA76ED395 FOREIGN KEY (user_id) REFERENCES sylius_admin_user (id)');
        $this->addSql('ALTER TABLE sylius_admin_api_auth_code DROP FOREIGN KEY FK_C840417919EB6921');
        $this->addSql('ALTER TABLE sylius_admin_api_auth_code DROP FOREIGN KEY FK_C8404179A76ED395');
        $this->addSql('DROP INDEX UNIQ_C84041795F37A13B ON sylius_admin_api_auth_code');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E366D8485F37A13B ON sylius_admin_api_auth_code (token)');
        $this->addSql('DROP INDEX IDX_C840417919EB6921 ON sylius_admin_api_auth_code');
        $this->addSql('CREATE INDEX IDX_E366D84819EB6921 ON sylius_admin_api_auth_code (client_id)');
        $this->addSql('DROP INDEX IDX_C8404179A76ED395 ON sylius_admin_api_auth_code');
        $this->addSql('CREATE INDEX IDX_E366D848A76ED395 ON sylius_admin_api_auth_code (user_id)');
        $this->addSql('ALTER TABLE sylius_admin_api_auth_code ADD CONSTRAINT FK_E366D84819EB6921 FOREIGN KEY (client_id) REFERENCES sylius_admin_api_client (id)');
        $this->addSql('ALTER TABLE sylius_admin_api_auth_code ADD CONSTRAINT FK_E366D848A76ED395 FOREIGN KEY (user_id) REFERENCES sylius_admin_user (id)');
        $this->addSql('ALTER TABLE sylius_admin_api_refresh_token DROP FOREIGN KEY FK_4457852519EB6921');
        $this->addSql('ALTER TABLE sylius_admin_api_refresh_token DROP FOREIGN KEY FK_44578525A76ED395');
        $this->addSql('DROP INDEX UNIQ_445785255F37A13B ON sylius_admin_api_refresh_token');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9160E3FA5F37A13B ON sylius_admin_api_refresh_token (token)');
        $this->addSql('DROP INDEX IDX_4457852519EB6921 ON sylius_admin_api_refresh_token');
        $this->addSql('CREATE INDEX IDX_9160E3FA19EB6921 ON sylius_admin_api_refresh_token (client_id)');
        $this->addSql('DROP INDEX IDX_44578525A76ED395 ON sylius_admin_api_refresh_token');
        $this->addSql('CREATE INDEX IDX_9160E3FAA76ED395 ON sylius_admin_api_refresh_token (user_id)');
        $this->addSql('ALTER TABLE sylius_admin_api_refresh_token ADD CONSTRAINT FK_9160E3FA19EB6921 FOREIGN KEY (client_id) REFERENCES sylius_admin_api_client (id)');
        $this->addSql('ALTER TABLE sylius_admin_api_refresh_token ADD CONSTRAINT FK_9160E3FAA76ED395 FOREIGN KEY (user_id) REFERENCES sylius_admin_user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_admin_api_access_token DROP FOREIGN KEY FK_2AA4915D19EB6921');
        $this->addSql('ALTER TABLE sylius_admin_api_access_token DROP FOREIGN KEY FK_2AA4915DA76ED395');
        $this->addSql('DROP INDEX UNIQ_2AA4915D5F37A13B ON sylius_admin_api_access_token');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7D83AA7F5F37A13B ON sylius_admin_api_access_token (token)');
        $this->addSql('DROP INDEX IDX_2AA4915D19EB6921 ON sylius_admin_api_access_token');
        $this->addSql('CREATE INDEX IDX_7D83AA7F19EB6921 ON sylius_admin_api_access_token (client_id)');
        $this->addSql('DROP INDEX IDX_2AA4915DA76ED395 ON sylius_admin_api_access_token');
        $this->addSql('CREATE INDEX IDX_7D83AA7FA76ED395 ON sylius_admin_api_access_token (user_id)');
        $this->addSql('ALTER TABLE sylius_admin_api_access_token ADD CONSTRAINT FK_7D83AA7F19EB6921 FOREIGN KEY (client_id) REFERENCES sylius_admin_api_client (id)');
        $this->addSql('ALTER TABLE sylius_admin_api_access_token ADD CONSTRAINT FK_7D83AA7FA76ED395 FOREIGN KEY (user_id) REFERENCES sylius_admin_user (id)');
        $this->addSql('ALTER TABLE sylius_admin_api_auth_code DROP FOREIGN KEY FK_E366D84819EB6921');
        $this->addSql('ALTER TABLE sylius_admin_api_auth_code DROP FOREIGN KEY FK_E366D848A76ED395');
        $this->addSql('DROP INDEX UNIQ_E366D8485F37A13B ON sylius_admin_api_auth_code');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C84041795F37A13B ON sylius_admin_api_auth_code (token)');
        $this->addSql('DROP INDEX IDX_E366D84819EB6921 ON sylius_admin_api_auth_code');
        $this->addSql('CREATE INDEX IDX_C840417919EB6921 ON sylius_admin_api_auth_code (client_id)');
        $this->addSql('DROP INDEX IDX_E366D848A76ED395 ON sylius_admin_api_auth_code');
        $this->addSql('CREATE INDEX IDX_C8404179A76ED395 ON sylius_admin_api_auth_code (user_id)');
        $this->addSql('ALTER TABLE sylius_admin_api_auth_code ADD CONSTRAINT FK_C840417919EB6921 FOREIGN KEY (client_id) REFERENCES sylius_admin_api_client (id)');
        $this->addSql('ALTER TABLE sylius_admin_api_auth_code ADD CONSTRAINT FK_C8404179A76ED395 FOREIGN KEY (user_id) REFERENCES sylius_admin_user (id)');
        $this->addSql('ALTER TABLE sylius_admin_api_refresh_token DROP FOREIGN KEY FK_9160E3FA19EB6921');
        $this->addSql('ALTER TABLE sylius_admin_api_refresh_token DROP FOREIGN KEY FK_9160E3FAA76ED395');
        $this->addSql('DROP INDEX UNIQ_9160E3FA5F37A13B ON sylius_admin_api_refresh_token');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_445785255F37A13B ON sylius_admin_api_refresh_token (token)');
        $this->addSql('DROP INDEX IDX_9160E3FA19EB6921 ON sylius_admin_api_refresh_token');
        $this->addSql('CREATE INDEX IDX_4457852519EB6921 ON sylius_admin_api_refresh_token (client_id)');
        $this->addSql('DROP INDEX IDX_9160E3FAA76ED395 ON sylius_admin_api_refresh_token');
        $this->addSql('CREATE INDEX IDX_44578525A76ED395 ON sylius_admin_api_refresh_token (user_id)');
        $this->addSql('ALTER TABLE sylius_admin_api_refresh_token ADD CONSTRAINT FK_4457852519EB6921 FOREIGN KEY (client_id) REFERENCES sylius_admin_api_client (id)');
        $this->addSql('ALTER TABLE sylius_admin_api_refresh_token ADD CONSTRAINT FK_44578525A76ED395 FOREIGN KEY (user_id) REFERENCES sylius_admin_user (id)');
        $this->addSql('ALTER TABLE sylius_admin_api_access_token RENAME sylius_api_access_token');
        $this->addSql('ALTER TABLE sylius_admin_api_auth_code RENAME sylius_api_auth_code');
        $this->addSql('ALTER TABLE sylius_admin_api_client RENAME sylius_api_client');
        $this->addSql('ALTER TABLE sylius_admin_api_refresh_token RENAME sylius_api_refresh_token');
    }
}
