<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140423211612 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE sylius_promotion_order_item (order_item_id INT NOT NULL, promotion_id INT NOT NULL, INDEX IDX_49838ED1E415FB15 (order_item_id), INDEX IDX_49838ED1139DF194 (promotion_id), PRIMARY KEY(order_item_id, promotion_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE sylius_promotion_order_item ADD CONSTRAINT FK_49838ED1E415FB15 FOREIGN KEY (order_item_id) REFERENCES sylius_order_item (id)");
        $this->addSql("ALTER TABLE sylius_promotion_order_item ADD CONSTRAINT FK_49838ED1139DF194 FOREIGN KEY (promotion_id) REFERENCES sylius_promotion (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE sylius_promotion_order_item");
    }
}
