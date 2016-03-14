<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160225115200 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_theme (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, authors LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', UNIQUE INDEX UNIQ_3CAD5695E237E06 (name), INDEX IDX_3CAD5695E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_theme_parents (child_id INT NOT NULL, parent_id INT NOT NULL, INDEX IDX_48942C67DD62C21B (child_id), INDEX IDX_48942C67727ACA70 (parent_id), PRIMARY KEY(child_id, parent_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_theme_parents ADD CONSTRAINT FK_48942C67DD62C21B FOREIGN KEY (child_id) REFERENCES sylius_theme (id)');
        $this->addSql('ALTER TABLE sylius_theme_parents ADD CONSTRAINT FK_48942C67727ACA70 FOREIGN KEY (parent_id) REFERENCES sylius_theme (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_theme_parents DROP FOREIGN KEY FK_48942C67DD62C21B');
        $this->addSql('ALTER TABLE sylius_theme_parents DROP FOREIGN KEY FK_48942C67727ACA70');
        $this->addSql('DROP TABLE sylius_theme');
        $this->addSql('DROP TABLE sylius_theme_parents');
    }
}
