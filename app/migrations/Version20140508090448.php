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
class Version20140508090448 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("CREATE TABLE sylius_user_oauth (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, provider VARCHAR(255) NOT NULL, identifier VARCHAR(255) NOT NULL, access_token VARCHAR(255) DEFAULT NULL, INDEX IDX_C3471B78A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE sylius_user_oauth ADD CONSTRAINT FK_C3471B78A76ED395 FOREIGN KEY (user_id) REFERENCES sylius_user (id)");
    }

    public function postUp(Schema $schema)
    {
        $this->addSql("INSERT INTO sylius_user_oauth (user_id, provider, identifier)
SELECT sylius_user.id, 'amazon', sylius_user.amazon_id
FROM sylius_user
WHERE sylius_user.amazon_id IS NOT NULL");

        $this->addSql("INSERT INTO sylius_user_oauth (user_id, provider, identifier)
SELECT sylius_user.id, 'facebook', sylius_user.facebook_id
FROM sylius_user
WHERE sylius_user.facebook_id IS NOT NULL");

        $this->addSql("INSERT INTO sylius_user_oauth (user_id, provider, identifier)
SELECT sylius_user.id, 'google', sylius_user.google_id
FROM sylius_user
WHERE sylius_user.google_id IS NOT NULL");

        $this->addSql("ALTER TABLE sylius_user DROP amazon_id, DROP facebook_id, DROP google_id");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE sylius_user ADD amazon_id VARCHAR(255) DEFAULT NULL, ADD facebook_id VARCHAR(255) DEFAULT NULL, ADD google_id VARCHAR(255) DEFAULT NULL");
    }

    public function postDown(Schema $schema)
    {
        $this->addSql("UPDATE sylius_user
JOIN sylius_user_oauth ON (sylius_user.id = sylius_user_oauth.user_id AND sylius_user_oauth.provider = 'amazon')
SET sylius_user.amazon_id = sylius_user_oauth.identifier");

        $this->addSql("UPDATE sylius_user
JOIN sylius_user_oauth ON (sylius_user.id = sylius_user_oauth.user_id AND sylius_user_oauth.provider = 'facebook')
SET sylius_user.facebook_id = sylius_user_oauth.identifier");

        $this->addSql("UPDATE sylius_user
JOIN sylius_user_oauth ON (sylius_user.id = sylius_user_oauth.user_id AND sylius_user_oauth.provider = 'google')
SET sylius_user.google_id = sylius_user_oauth.identifier");

        $this->addSql("DROP TABLE sylius_user_oauth");
    }
}
