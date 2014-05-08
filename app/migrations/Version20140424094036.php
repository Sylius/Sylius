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
use Sylius\Component\Order\Model\OrderInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140424094036 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE sylius_order CHANGE state state VARCHAR(255) NOT NULL");
    }

    public function postUp(Schema $schema)
    {
        $this->addSql(sprintf("UPDATE sylius_order SET state = CASE state
            WHEN 1 THEN '%s'
            WHEN 2 THEN '%s'
            WHEN 3 THEN '%s'
            WHEN 4 THEN '%s'
            WHEN 5 THEN '%s'
            WHEN 6 THEN '%s'
            WHEN 7 THEN '%s'
            WHEN 8 THEN '%s' END",

            OrderInterface::STATE_CART,
            OrderInterface::STATE_CART_LOCKED,
            OrderInterface::STATE_PENDING,
            OrderInterface::STATE_CONFIRMED,
            OrderInterface::STATE_SHIPPED,
            OrderInterface::STATE_ABANDONED,
            OrderInterface::STATE_CANCELLED,
            OrderInterface::STATE_RETURNED
        ));
    }

    public function preDown(Schema $schema)
    {
        $this->addSql(sprintf("UPDATE sylius_order SET state = CASE state
            WHEN '%s' THEN 1
            WHEN '%s' THEN 2
            WHEN '%s' THEN 3
            WHEN '%s' THEN 4
            WHEN '%s' THEN 5
            WHEN '%s' THEN 6
            WHEN '%s' THEN 7
            WHEN '%s' THEN 8 END",

            OrderInterface::STATE_CART,
            OrderInterface::STATE_CART_LOCKED,
            OrderInterface::STATE_PENDING,
            OrderInterface::STATE_CONFIRMED,
            OrderInterface::STATE_SHIPPED,
            OrderInterface::STATE_ABANDONED,
            OrderInterface::STATE_CANCELLED,
            OrderInterface::STATE_RETURNED
        ));
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE sylius_order CHANGE state state INT NOT NULL");
    }
}
