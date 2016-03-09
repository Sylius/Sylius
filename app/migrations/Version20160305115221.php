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
class Version20160305115221 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_promotion_coupon DROP deleted_at');
        $this->addSql('ALTER TABLE sylius_order DROP FOREIGN KEY FK_6196A1F917B24436');
        $this->addSql('ALTER TABLE sylius_order ADD CONSTRAINT FK_6196A1F917B24436 FOREIGN KEY (promotion_coupon_id) REFERENCES sylius_promotion_coupon (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_order DROP FOREIGN KEY FK_6196A1F917B24436');
        $this->addSql('ALTER TABLE sylius_order ADD CONSTRAINT FK_6196A1F917B24436 FOREIGN KEY (promotion_coupon_id) REFERENCES sylius_promotion_coupon (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE sylius_promotion_coupon ADD deleted_at DATETIME DEFAULT NULL');
    }
}
