<?php

declare(strict_types=1);

namespace Sylius\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170110120125 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_payment DROP FOREIGN KEY FK_D9191BD419883967');
        $this->addSql('ALTER TABLE sylius_payment ADD CONSTRAINT FK_D9191BD419883967 FOREIGN KEY (method_id) REFERENCES sylius_payment_method (id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_payment DROP FOREIGN KEY FK_D9191BD419883967');
        $this->addSql('ALTER TABLE sylius_payment ADD CONSTRAINT FK_D9191BD419883967 FOREIGN KEY (method_id) REFERENCES sylius_payment_method (id) ON DELETE SET NULL');
    }
}
