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
class Version20140710152234 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', "Migration can only be executed safely on 'mysql'.");

        $this->addSql('ALTER TABLE sylius_address ADD user_id INT DEFAULT NULL AFTER postcode ;');
        $this->addSql('ALTER TABLE sylius_address ADD CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES sylius_user (id);');
        $this->addSql('CREATE INDEX idx_user ON sylius_address (user_id);');
        $this->addSql('UPDATE sylius_address SET user_id = (SELECT user_id FROM sylius_user_address WHERE address_id = sylius_address.id);');
        $this->addSql('DROP TABLE sylius_user_address;');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', "Migration can only be executed safely on 'mysql'.");

        $this->addSql('CREATE TABLE sylius_user_address( user_id INT NOT NULL, address_id INT NOT NULL, PRIMARY KEY ( user_id, address_id ), FOREIGN KEY ( user_id ) REFERENCES sylius_user ( id ) ON DELETE CASCADE, FOREIGN KEY ( address_id ) REFERENCES sylius_address ( id ) ON DELETE CASCADE);');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9185794AF5B7AF75 ON sylius_user_address ( address_id );');
        $this->addSql('CREATE INDEX IDX_9185794AA76ED395 ON sylius_user_address ( user_id );');
        $this->addSql('INSERT INTO sylius_user_address (user_id, address_id) SELECT user_id, id FROM sylius_address WHERE user_id IS NOT NULL;');
        $this->addSql('ALTER TABLE sylius_address DROP FOREIGN KEY fk_user;');
        $this->addSql('DROP INDEX idx_user ON sylius_address;');
        $this->addSql('ALTER TABLE sylius_address DROP user_id;');
    }
}
