<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160427100800 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_channel DROP FOREIGN KEY FK_16C8119E59027487');
        $this->addSql('ALTER TABLE sylius_theme_parents DROP FOREIGN KEY FK_48942C67727ACA70');
        $this->addSql('ALTER TABLE sylius_theme_parents DROP FOREIGN KEY FK_48942C67DD62C21B');
        $this->addSql('DROP TABLE sylius_theme');
        $this->addSql('DROP TABLE sylius_theme_parents');
        $this->addSql('DROP INDEX IDX_16C8119E59027487 ON sylius_channel');
        $this->addSql('ALTER TABLE sylius_channel ADD theme_name VARCHAR(255) DEFAULT NULL, DROP theme_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_theme (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, path VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, code VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, description VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, authors LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:object)\', screenshots LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_3CAD5695E237E06 (name), INDEX IDX_3CAD5695E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_theme_parents (child_id INT NOT NULL, parent_id INT NOT NULL, INDEX IDX_48942C67DD62C21B (child_id), INDEX IDX_48942C67727ACA70 (parent_id), PRIMARY KEY(child_id, parent_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_theme_parents ADD CONSTRAINT FK_48942C67727ACA70 FOREIGN KEY (parent_id) REFERENCES sylius_theme (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_theme_parents ADD CONSTRAINT FK_48942C67DD62C21B FOREIGN KEY (child_id) REFERENCES sylius_theme (id)');
        $this->addSql('ALTER TABLE sylius_channel ADD theme_id INT DEFAULT NULL, DROP theme_name');
        $this->addSql('ALTER TABLE sylius_channel ADD CONSTRAINT FK_16C8119E59027487 FOREIGN KEY (theme_id) REFERENCES sylius_theme (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_16C8119E59027487 ON sylius_channel (theme_id)');
    }
}
