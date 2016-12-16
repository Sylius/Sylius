<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20161214153137 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $defaultLocale = $this->container->getParameter('locale');

        $this->addSql('CREATE TABLE sylius_product_variant_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_8DC18EDC2C2AC5D3 (translatable_id), UNIQUE INDEX sylius_product_variant_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_product_variant_translation ADD CONSTRAINT FK_8DC18EDC2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES sylius_product_variant (id) ON DELETE CASCADE');
        $this->addSql('INSERT INTO sylius_product_variant_translation (translatable_id, name, locale) SELECT id, name, "'.$defaultLocale.'" from sylius_product_variant WHERE sylius_product_variant.name IS NOT null');
        $this->addSql('ALTER TABLE sylius_product_variant DROP name');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $defaultLocale = $this->container->getParameter('locale');

        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product_variant ADD name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('UPDATE sylius_product_variant SET name = (SELECT name FROM sylius_product_variant_translation WHERE sylius_product_variant_translation.translatable_id = sylius_product_variant.id AND sylius_product_variant_translation.locale = "'.$defaultLocale.'")');
        $this->addSql('DROP TABLE sylius_product_variant_translation');
    }
}
