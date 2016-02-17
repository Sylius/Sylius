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
class Version20160211130526 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sylius_promotion_coupon_order');
        $this->addSql('DROP INDEX fulltext_search_idx ON sylius_search_index');
        $this->addSql('CREATE INDEX fulltext_search_idx ON sylius_search_index (item_id)');
        $this->addSql('ALTER TABLE sylius_order ADD promotion_coupon_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_order ADD CONSTRAINT FK_6196A1F917B24436 FOREIGN KEY (promotion_coupon_id) REFERENCES sylius_promotion_coupon (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6196A1F917B24436 ON sylius_order (promotion_coupon_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_promotion_coupon_order (order_id INT NOT NULL, promotion_coupon_id INT NOT NULL, INDEX IDX_D58E3BC48D9F6D38 (order_id), INDEX IDX_D58E3BC417B24436 (promotion_coupon_id), PRIMARY KEY(order_id, promotion_coupon_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_promotion_coupon_order ADD CONSTRAINT FK_D58E3BC417B24436 FOREIGN KEY (promotion_coupon_id) REFERENCES sylius_promotion_coupon (id)');
        $this->addSql('ALTER TABLE sylius_promotion_coupon_order ADD CONSTRAINT FK_D58E3BC48D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id)');
        $this->addSql('ALTER TABLE sylius_order DROP FOREIGN KEY FK_6196A1F917B24436');
        $this->addSql('DROP INDEX UNIQ_6196A1F917B24436 ON sylius_order');
        $this->addSql('ALTER TABLE sylius_order DROP promotion_coupon_id');
        $this->addSql('DROP INDEX fulltext_search_idx ON sylius_search_index');
        $this->addSql('CREATE FULLTEXT INDEX fulltext_search_idx ON sylius_search_index (value)');
    }
}
