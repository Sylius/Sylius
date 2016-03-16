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
class Version20150531210410 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_customer (id INT AUTO_INCREMENT NOT NULL, billing_address_id INT DEFAULT NULL, shipping_address_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, birthday DATETIME DEFAULT NULL, gender VARCHAR(1) DEFAULT \'u\' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, currency VARCHAR(3) DEFAULT NULL, UNIQUE INDEX UNIQ_7E82D5E679D0C0E4 (billing_address_id), UNIQUE INDEX UNIQ_7E82D5E64D4CFF2B (shipping_address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_customer_group (customer_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_7FCF9B059395C3F3 (customer_id), INDEX IDX_7FCF9B05FE54D947 (group_id), PRIMARY KEY(customer_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_customer ADD CONSTRAINT FK_7E82D5E679D0C0E4 FOREIGN KEY (billing_address_id) REFERENCES sylius_address (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE sylius_customer ADD CONSTRAINT FK_7E82D5E64D4CFF2B FOREIGN KEY (shipping_address_id) REFERENCES sylius_address (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE sylius_customer_group ADD CONSTRAINT FK_7FCF9B059395C3F3 FOREIGN KEY (customer_id) REFERENCES sylius_customer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_customer_group ADD CONSTRAINT FK_7FCF9B05FE54D947 FOREIGN KEY (group_id) REFERENCES sylius_group (id) ON DELETE CASCADE');

        // create customers based on users
        $this->addSql('INSERT INTO sylius_customer (billing_address_id, shipping_address_id, email, email_canonical, first_name, last_name, created_at, updated_at, deleted_at, currency) SELECT billing_address_id, shipping_address_id, email, email_canonical, first_name, last_name, created_at, updated_at, deleted_at, currency FROM sylius_user');

        // associate users with customers by email
        $this->addSql('ALTER TABLE sylius_user ADD customer_id INT NOT NULL');
        $this->addSql('UPDATE sylius_user INNER JOIN sylius_customer ON sylius_user.email = sylius_customer.email SET sylius_user.customer_id = sylius_customer.id');
        $this->addSql('ALTER TABLE sylius_user ADD CONSTRAINT FK_569A33C09395C3F3 FOREIGN KEY (customer_id) REFERENCES sylius_customer (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_569A33C09395C3F3 ON sylius_user (customer_id)');

        // migrate groups
        $this->addSql('INSERT INTO sylius_customer_group (customer_id, group_id) SELECT sylius_customer.id, sylius_user_group.group_id FROM sylius_user_group INNER JOIN sylius_user ON sylius_user_group.user_id = sylius_user.id INNER JOIN sylius_customer ON sylius_user.customer_id = sylius_customer.id');
        $this->addSql('DROP TABLE sylius_user_group');
        $this->addSql('DROP INDEX UNIQ_F97F76A45E237E06 ON sylius_group');
        $this->addSql('ALTER TABLE sylius_group DROP roles');

        // migrate addresses
        $this->addSql('ALTER TABLE sylius_address DROP FOREIGN KEY FK_B97FF058A76ED395');
        $this->addSql('DROP INDEX IDX_B97FF058A76ED395 ON sylius_address');
        $this->addSql('ALTER TABLE sylius_address ADD customer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_address ADD CONSTRAINT FK_B97FF0589395C3F3 FOREIGN KEY (customer_id) REFERENCES sylius_customer (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_B97FF0589395C3F3 ON sylius_address (customer_id)');
        // associate addresses with customers
        $this->addSql('UPDATE sylius_address INNER JOIN sylius_user ON sylius_address.user_id = sylius_user.id INNER JOIN sylius_customer ON sylius_user.customer_id = sylius_customer.id SET sylius_address.customer_id = sylius_customer.id');
        // drop relation to user
        $this->addSql('ALTER TABLE sylius_address DROP user_id');

        $this->addSql('ALTER TABLE sylius_promotion_coupon CHANGE per_user_usage_limit per_customer_usage_limit INT DEFAULT NULL');

        // migrate orders
        $this->addSql('ALTER TABLE sylius_order DROP FOREIGN KEY FK_6196A1F9A76ED395');
        $this->addSql('DROP INDEX IDX_6196A1F9A76ED395 ON sylius_order');
        $this->addSql('ALTER TABLE sylius_order ADD customer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_order ADD CONSTRAINT FK_6196A1F99395C3F3 FOREIGN KEY (customer_id) REFERENCES sylius_customer (id)');
        $this->addSql('CREATE INDEX IDX_6196A1F99395C3F3 ON sylius_order (customer_id)');
        // create customers based on guest checkouts
        $this->addSql('INSERT INTO sylius_customer (email, email_canonical, created_at) SELECT sylius_order.email, sylius_order.email, sylius_order.created_at FROM sylius_order LEFT JOIN sylius_customer ON sylius_order.email = sylius_customer.email WHERE sylius_customer.email IS NULL AND sylius_order.email IS NOT NULL');
        // associate orders with customers
        $this->addSql('UPDATE sylius_order INNER JOIN sylius_user ON sylius_order.user_id = sylius_user.id INNER JOIN sylius_customer ON sylius_user.customer_id = sylius_customer.id SET sylius_order.customer_id = sylius_customer.id');
        // associate guest orders with customers
        $this->addSql('UPDATE sylius_order INNER JOIN sylius_customer ON sylius_order.email = sylius_customer.email SET sylius_order.customer_id = sylius_customer.id');

        $this->addSql('ALTER TABLE sylius_order DROP email, DROP user_id');
        $this->addSql('ALTER TABLE sylius_user DROP FOREIGN KEY FK_569A33C04D4CFF2B');
        $this->addSql('ALTER TABLE sylius_user DROP FOREIGN KEY FK_569A33C079D0C0E4');
        $this->addSql('DROP INDEX UNIQ_569A33C092FC23A8 ON sylius_user');
        $this->addSql('DROP INDEX UNIQ_569A33C0A0D96FBF ON sylius_user');
        $this->addSql('DROP INDEX UNIQ_569A33C079D0C0E4 ON sylius_user');
        $this->addSql('DROP INDEX UNIQ_569A33C04D4CFF2B ON sylius_user');
        $this->addSql('ALTER TABLE sylius_user DROP email, DROP shipping_address_id, DROP billing_address_id, DROP email_canonical, DROP expired, DROP credentials_expired, DROP currency, DROP first_name, DROP last_name, CHANGE username username VARCHAR(255) DEFAULT NULL, CHANGE username_canonical username_canonical VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_address DROP FOREIGN KEY FK_B97FF0589395C3F3');
        $this->addSql('ALTER TABLE sylius_customer_group DROP FOREIGN KEY FK_7FCF9B059395C3F3');
        $this->addSql('ALTER TABLE sylius_order DROP FOREIGN KEY FK_6196A1F99395C3F3');
        $this->addSql('ALTER TABLE sylius_user DROP FOREIGN KEY FK_569A33C09395C3F3');
        $this->addSql('CREATE TABLE sylius_user_group (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_AA5D2779A76ED395 (user_id), INDEX IDX_AA5D2779FE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_user_group ADD CONSTRAINT FK_AA5D2779FE54D947 FOREIGN KEY (group_id) REFERENCES sylius_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_user_group ADD CONSTRAINT FK_AA5D2779A76ED395 FOREIGN KEY (user_id) REFERENCES sylius_user (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE sylius_customer');
        $this->addSql('DROP TABLE sylius_customer_group');
        $this->addSql('DROP INDEX IDX_B97FF0589395C3F3 ON sylius_address');
        $this->addSql('ALTER TABLE sylius_address DROP customer_id, ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_address ADD CONSTRAINT FK_B97FF058A76ED395 FOREIGN KEY (user_id) REFERENCES sylius_user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_B97FF058A76ED395 ON sylius_address (user_id)');
        $this->addSql('ALTER TABLE sylius_group ADD roles LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F97F76A45E237E06 ON sylius_group (name)');
        $this->addSql('DROP INDEX IDX_6196A1F99395C3F3 ON sylius_order');
        $this->addSql('ALTER TABLE sylius_order ADD email VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD user_id INT DEFAULT NULL, DROP customer_id');
        $this->addSql('ALTER TABLE sylius_order ADD CONSTRAINT FK_6196A1F9A76ED395 FOREIGN KEY (user_id) REFERENCES sylius_user (id)');
        $this->addSql('CREATE INDEX IDX_6196A1F9A76ED395 ON sylius_order (user_id)');
        $this->addSql('ALTER TABLE sylius_promotion_coupon CHANGE per_customer_usage_limit per_user_usage_limit INT DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_569A33C09395C3F3 ON sylius_user');
        $this->addSql('ALTER TABLE sylius_user ADD shipping_address_id INT DEFAULT NULL, ADD billing_address_id INT DEFAULT NULL, ADD email VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD email_canonical VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD expired TINYINT(1) NOT NULL, ADD credentials_expired TINYINT(1) NOT NULL, ADD currency VARCHAR(3) DEFAULT NULL COLLATE utf8_unicode_ci, ADD first_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD last_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP customer_id, CHANGE username username VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE username_canonical username_canonical VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE sylius_user ADD CONSTRAINT FK_569A33C04D4CFF2B FOREIGN KEY (shipping_address_id) REFERENCES sylius_address (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE sylius_user ADD CONSTRAINT FK_569A33C079D0C0E4 FOREIGN KEY (billing_address_id) REFERENCES sylius_address (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_569A33C092FC23A8 ON sylius_user (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_569A33C079D0C0E4 ON sylius_user (billing_address_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_569A33C04D4CFF2B ON sylius_user (shipping_address_id)');
    }
}
