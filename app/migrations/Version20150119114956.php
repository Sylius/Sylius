<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add Store entity
 */
class Version20150119114956 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql(
            'CREATE TABLE smile_store (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_42F9FEDF77153098 (code), UNIQUE INDEX UNIQ_42F9FEDFF47645AE (url), INDEX IDX_42F9FEDF727ACA70 (parent_id), INDEX IDX_42F9FEDF77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;'
        );
        $this->addSql(
            'ALTER TABLE smile_store ADD CONSTRAINT FK_42F9FEDF727ACA70 FOREIGN KEY (parent_id) REFERENCES smile_store (id);'
        );
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE smile_store');
    }
}
