<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251031072930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE planet_pay_payment_notification_planet_pay_payment_notification_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE planet_pay_payment_refund_notification_payment_refund_notification_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE planet_pay_payment_notification (planet_pay_payment_notification_id INT NOT NULL, order_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, transaction_id VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, additional_info TEXT DEFAULT NULL, PRIMARY KEY(planet_pay_payment_notification_id))');
        $this->addSql('CREATE INDEX IDX_AFB413F38D9F6D38 ON planet_pay_payment_notification (order_id)');
        $this->addSql('CREATE TABLE planet_pay_payment_refund_notification (payment_refund_notification_id INT NOT NULL, order_id INT NOT NULL, transaction_id VARCHAR(255) NOT NULL, refund_id VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, amount NUMERIC(20, 4) NOT NULL, reject_code VARCHAR(255) DEFAULT NULL, reject_info VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(payment_refund_notification_id))');
        $this->addSql('CREATE INDEX IDX_69E5A87B8D9F6D38 ON planet_pay_payment_refund_notification (order_id)');
        $this->addSql('ALTER TABLE planet_pay_payment_notification ADD CONSTRAINT FK_AFB413F38D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE planet_pay_payment_refund_notification ADD CONSTRAINT FK_69E5A87B8D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE planet_pay_payment_notification_planet_pay_payment_notification_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE planet_pay_payment_refund_notification_payment_refund_notification_id_seq CASCADE');
        $this->addSql('ALTER TABLE planet_pay_payment_notification DROP CONSTRAINT FK_AFB413F38D9F6D38');
        $this->addSql('ALTER TABLE planet_pay_payment_refund_notification DROP CONSTRAINT FK_69E5A87B8D9F6D38');
        $this->addSql('DROP TABLE planet_pay_payment_notification');
        $this->addSql('DROP TABLE planet_pay_payment_refund_notification');
    }
}
