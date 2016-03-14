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
class Version20160226091855 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_order DROP INDEX IDX_6196A1F94D4CFF2B, ADD UNIQUE INDEX UNIQ_6196A1F94D4CFF2B (shipping_address_id)');
        $this->addSql('ALTER TABLE sylius_order DROP INDEX IDX_6196A1F979D0C0E4, ADD UNIQUE INDEX UNIQ_6196A1F979D0C0E4 (billing_address_id)');
        $this->addSql('ALTER TABLE sylius_order DROP deleted_at');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_order DROP INDEX UNIQ_6196A1F94D4CFF2B, ADD INDEX IDX_6196A1F94D4CFF2B (shipping_address_id)');
        $this->addSql('ALTER TABLE sylius_order DROP INDEX UNIQ_6196A1F979D0C0E4, ADD INDEX IDX_6196A1F979D0C0E4 (billing_address_id)');
        $this->addSql('ALTER TABLE sylius_order ADD deleted_at DATETIME DEFAULT NULL');
    }
}
