<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminApiBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200918223714 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE FULLTEXT INDEX IDX_7E82D5E6E7927C74A9D1C132C808BA5A ON sylius_customer (email, first_name, last_name)');
        $this->addSql('CREATE FULLTEXT INDEX IDX_105A9085E237E066DE44026 ON sylius_product_translation (name, description)');
        $this->addSql('CREATE FULLTEXT INDEX IDX_1487DFCF5E237E066DE44026 ON sylius_taxon_translation (name, description)');
        $this->addSql('CREATE FULLTEXT INDEX IDX_CBA491AD5E237E06 ON sylius_product_option_translation (name)');
        $this->addSql('CREATE FULLTEXT INDEX IDX_93850EBA5E237E06 ON sylius_product_attribute_translation (name)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX IDX_7E82D5E6E7927C74A9D1C132C808BA5A ON sylius_customer');
        $this->addSql('DROP INDEX IDX_105A9085E237E066DE44026 ON sylius_product_translation');
        $this->addSql('DROP INDEX IDX_1487DFCF5E237E066DE44026 ON sylius_taxon_translation');
        $this->addSql('DROP INDEX IDX_CBA491AD5E237E06 ON sylius_product_option_translation');
        $this->addSql('DROP INDEX IDX_93850EBA5E237E06 ON sylius_product_attribute_translation');
    }
}
