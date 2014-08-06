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
class Version20140708143228 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("CREATE TABLE sylius_promotion_coupon_order (order_id INT NOT NULL, promotion_coupon_id INT NOT NULL, INDEX IDX_B97AAD798D9F6D38 (order_id), UNIQUE INDEX UNIQ_B97AAD7917B24436 (promotion_coupon_id), PRIMARY KEY(order_id, promotion_coupon_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE sylius_promotion_coupon_order ADD CONSTRAINT FK_B97AAD798D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id)");
        $this->addSql("ALTER TABLE sylius_promotion_coupon_order ADD CONSTRAINT FK_B97AAD7917B24436 FOREIGN KEY (promotion_coupon_id) REFERENCES sylius_promotion_coupon (id)");

        $this->addSql("INSERT INTO sylius_promotion_coupon_order (order_id, promotion_coupon_id)
            SELECT sylius_order.id, sylius_order.coupon_id
            FROM sylius_order
            WHERE sylius_order.coupon_id IS NOT NULL
        ");

        $this->addSql("ALTER TABLE sylius_order DROP FOREIGN KEY FK_6196A1F966C5951B");
        $this->addSql("DROP INDEX IDX_6196A1F966C5951B ON sylius_order");
        $this->addSql("ALTER TABLE sylius_order DROP coupon_id");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("DROP TABLE sylius_promotion_coupon_order");
        $this->addSql("ALTER TABLE sylius_order ADD coupon_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE sylius_order ADD CONSTRAINT FK_6196A1F966C5951B FOREIGN KEY (coupon_id) REFERENCES sylius_promotion_coupon (id) ON DELETE SET NULL");
        $this->addSql("CREATE INDEX IDX_6196A1F966C5951B ON sylius_order (coupon_id)");
    }
}
