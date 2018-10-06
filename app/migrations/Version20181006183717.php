<?php declare(strict_types=1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181006183717 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_order_item CHANGE units_total units_total BIGINT NOT NULL, CHANGE adjustments_total adjustments_total BIGINT NOT NULL, CHANGE total total BIGINT NOT NULL');
        $this->addSql('ALTER TABLE sylius_order_item_unit CHANGE adjustments_total adjustments_total BIGINT NOT NULL');
        $this->addSql('ALTER TABLE sylius_order CHANGE items_total items_total BIGINT NOT NULL, CHANGE adjustments_total adjustments_total BIGINT NOT NULL, CHANGE total total BIGINT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_order CHANGE items_total items_total INT NOT NULL, CHANGE adjustments_total adjustments_total INT NOT NULL, CHANGE total total INT NOT NULL');
        $this->addSql('ALTER TABLE sylius_order_item CHANGE units_total units_total INT NOT NULL, CHANGE adjustments_total adjustments_total INT NOT NULL, CHANGE total total INT NOT NULL');
        $this->addSql('ALTER TABLE sylius_order_item_unit CHANGE adjustments_total adjustments_total INT NOT NULL');
    }
}
