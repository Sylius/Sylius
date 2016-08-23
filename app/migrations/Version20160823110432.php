<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160823110432 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_permission DROP FOREIGN KEY FK_C5160A4E727ACA70');
        $this->addSql('ALTER TABLE sylius_role_permission DROP FOREIGN KEY FK_45CEE9B8FED90CCA');
        $this->addSql('ALTER TABLE sylius_admin_user_role DROP FOREIGN KEY FK_EEACD2AFD60322AC');
        $this->addSql('ALTER TABLE sylius_role DROP FOREIGN KEY FK_8C606FE3727ACA70');
        $this->addSql('ALTER TABLE sylius_role_permission DROP FOREIGN KEY FK_45CEE9B8D60322AC');
        $this->addSql('ALTER TABLE sylius_shop_user_role DROP FOREIGN KEY FK_E865E2D3D60322AC');
        $this->addSql('DROP TABLE sylius_admin_user_role');
        $this->addSql('DROP TABLE sylius_permission');
        $this->addSql('DROP TABLE sylius_role');
        $this->addSql('DROP TABLE sylius_role_permission');
        $this->addSql('DROP TABLE sylius_shop_user_role');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_admin_user_role (user_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_EEACD2AFA76ED395 (user_id), INDEX IDX_EEACD2AFD60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_permission (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, description LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, tree_left INT NOT NULL, tree_right INT NOT NULL, tree_level INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_C5160A4E727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_role (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, description LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, security_roles LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\', tree_left INT NOT NULL, tree_right INT NOT NULL, tree_level INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_8C606FE3727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_role_permission (role_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_45CEE9B8D60322AC (role_id), INDEX IDX_45CEE9B8FED90CCA (permission_id), PRIMARY KEY(role_id, permission_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_shop_user_role (user_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_E865E2D3A76ED395 (user_id), INDEX IDX_E865E2D3D60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_admin_user_role ADD CONSTRAINT FK_EEACD2AFA76ED395 FOREIGN KEY (user_id) REFERENCES sylius_admin_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_admin_user_role ADD CONSTRAINT FK_EEACD2AFD60322AC FOREIGN KEY (role_id) REFERENCES sylius_role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_permission ADD CONSTRAINT FK_C5160A4E727ACA70 FOREIGN KEY (parent_id) REFERENCES sylius_permission (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_role ADD CONSTRAINT FK_8C606FE3727ACA70 FOREIGN KEY (parent_id) REFERENCES sylius_role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_role_permission ADD CONSTRAINT FK_45CEE9B8D60322AC FOREIGN KEY (role_id) REFERENCES sylius_role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_role_permission ADD CONSTRAINT FK_45CEE9B8FED90CCA FOREIGN KEY (permission_id) REFERENCES sylius_permission (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_shop_user_role ADD CONSTRAINT FK_E865E2D3A76ED395 FOREIGN KEY (user_id) REFERENCES sylius_shop_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_shop_user_role ADD CONSTRAINT FK_E865E2D3D60322AC FOREIGN KEY (role_id) REFERENCES sylius_role (id) ON DELETE CASCADE');
    }
}
