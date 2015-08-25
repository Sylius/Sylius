<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150825160125 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_review DROP FOREIGN KEY FK_43626F3A4584665A');
        $this->addSql('DROP INDEX IDX_43626F3A4584665A ON sylius_review');
        $this->addSql('ALTER TABLE sylius_review CHANGE comment comment LONGTEXT DEFAULT NULL, CHANGE product_id review_subject_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_review ADD CONSTRAINT FK_43626F3A75AE1D8A FOREIGN KEY (review_subject_id) REFERENCES sylius_product (id)');
        $this->addSql('CREATE INDEX IDX_43626F3A75AE1D8A ON sylius_review (review_subject_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_review DROP FOREIGN KEY FK_43626F3A75AE1D8A');
        $this->addSql('DROP INDEX IDX_43626F3A75AE1D8A ON sylius_review');
        $this->addSql('ALTER TABLE sylius_review CHANGE comment comment LONGTEXT NOT NULL COLLATE utf8_unicode_ci, CHANGE review_subject_id product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_review ADD CONSTRAINT FK_43626F3A4584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id)');
        $this->addSql('CREATE INDEX IDX_43626F3A4584665A ON sylius_review (product_id)');
    }
}
