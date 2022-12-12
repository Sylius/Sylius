<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221212095719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make users\' salt nullable';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sylius_admin_user CHANGE salt salt VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_shop_user CHANGE salt salt VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sylius_admin_user CHANGE salt salt VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE sylius_shop_user CHANGE salt salt VARCHAR(255) NOT NULL');
    }
}
