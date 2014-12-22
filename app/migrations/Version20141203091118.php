<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141203091118 extends AbstractMigration implements ContainerAwareInterface
{
    private $container;
    private $defaultLocale;

    public function setContainer(ContainerInterface $container = null)
    {
        if (null !== $container) {
            $this->container     = $container;
            $this->defaultLocale = $container->getParameter('sylius.locale');
        }
    }

    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->connection->executeQuery('CREATE TABLE sylius_product_attribute_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, presentation VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_93850EBA2C2AC5D3 (translatable_id), UNIQUE INDEX sylius_product_attribute_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $productAttributes = $this->connection->fetchAll('SELECT * FROM sylius_product_attribute');
        foreach ($productAttributes as $productAttribute) {
            $this->connection->insert('sylius_product_attribute_translation', array(
                'presentation'    => $productAttribute['presentation'],
                'translatable_id' => $productAttribute['id'],
                'locale'          => $this->defaultLocale
            ));
        }

        $this->connection->executeQuery('CREATE TABLE sylius_product_option_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, presentation VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_CBA491AD2C2AC5D3 (translatable_id), UNIQUE INDEX sylius_product_option_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $productOptions = $this->connection->fetchAll('SELECT * FROM sylius_product_option');
        foreach ($productOptions as $productOption) {
            $this->connection->insert('sylius_product_option_translation', array(
                'presentation'    => $productOption['presentation'],
                'translatable_id' => $productOption['id'],
                'locale'          => $this->defaultLocale
            ));
        }

        $this->connection->executeQuery('CREATE TABLE sylius_country_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_B8BD1DDC2C2AC5D3 (translatable_id), UNIQUE INDEX sylius_country_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $countries = $this->connection->fetchAll('SELECT * FROM sylius_country');
        foreach ($countries as $country) {
            $this->connection->insert('sylius_country_translation', array(
                    'name'            => $country['name'],
                    'translatable_id' => $country['id'],
                    'locale'          => $this->defaultLocale)
            );
        }

        $this->connection->executeQuery('CREATE TABLE sylius_product_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, meta_keywords VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, locale VARCHAR(255) NOT NULL, short_description VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_105A908989D9B62 (slug), INDEX IDX_105A9082C2AC5D3 (translatable_id), UNIQUE INDEX sylius_product_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $syliusProducts = $this->connection->fetchAll('SELECT * FROM sylius_product');
        foreach ($syliusProducts as $syliusProduct) {
            $this->connection->insert('sylius_product_translation', array(
                'name'              => $syliusProduct['name'],
                'slug'              => $syliusProduct['slug'],
                'description'       => $syliusProduct['description'],
                'meta_keywords'     => $syliusProduct['meta_keywords'],
                'meta_description'  => $syliusProduct['meta_description'],
                'short_description' => $syliusProduct['short_description'],
                'translatable_id'   => $syliusProduct['id'],
                'locale'            => $this->defaultLocale
            ));
        }

        $this->connection->executeQuery('CREATE TABLE sylius_shipping_method_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_2B37DB3D2C2AC5D3 (translatable_id), UNIQUE INDEX sylius_shipping_method_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $shippingMethods = $this->connection->fetchAll('SELECT * FROM sylius_shipping_method');
        foreach ($shippingMethods as $shippingMethod) {
            $this->connection->insert('sylius_shipping_method_translation', array(
                'name'            => $shippingMethod['name'],
                'translatable_id' => $shippingMethod['id'],
                'locale'          => $this->defaultLocale
            ));
        }

        $this->connection->executeQuery('CREATE TABLE sylius_taxonomy_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_9F3F90D92C2AC5D3 (translatable_id), UNIQUE INDEX sylius_taxonomy_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $syliusTaxonomies = $this->connection->fetchAll('SELECT * FROM sylius_taxonomy');
        foreach ($syliusTaxonomies as $syliusTaxonomy) {
            $this->connection->insert('sylius_taxonomy_translation', array(
                'name'            => $syliusTaxonomy['name'],
                'translatable_id' => $syliusTaxonomy['id'],
                'locale'          => $this->defaultLocale
            ));
        }

        $this->connection->executeQuery('CREATE TABLE sylius_taxon_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, permalink VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, locale VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1487DFCF989D9B62 (slug), UNIQUE INDEX UNIQ_1487DFCFF286BC32 (permalink), INDEX IDX_1487DFCF2C2AC5D3 (translatable_id), UNIQUE INDEX sylius_taxon_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $taxons = $this->connection->fetchAll('SELECT * FROM sylius_taxon');
        foreach ($taxons as $taxon) {
            $this->connection->insert('sylius_taxon_translation', array(
                'name'            => $taxon['name'],
                'slug'            => $taxon['slug'],
                'permalink'       => $taxon['permalink'],
                'description'     => $taxon['description'],
                'translatable_id' => $taxon['id'],
                'locale'          => $this->defaultLocale
            ));
        }

        $this->addSql('ALTER TABLE sylius_product_attribute DROP presentation');
        $this->addSql('ALTER TABLE sylius_product_option DROP presentation');
        $this->addSql('ALTER TABLE sylius_country DROP name');
        $this->addSql('DROP INDEX UNIQ_677B9B74989D9B62 ON sylius_product');
        $this->addSql('ALTER TABLE sylius_product DROP name, DROP slug, DROP description, DROP meta_keywords, DROP meta_description, DROP short_description');
        $this->addSql('ALTER TABLE sylius_shipping_method DROP name');
        $this->addSql('DROP INDEX UNIQ_CFD811CA989D9B62 ON sylius_taxon');
        $this->addSql('DROP INDEX UNIQ_CFD811CAF286BC32 ON sylius_taxon');
        $this->addSql('ALTER TABLE sylius_taxon DROP name, DROP slug, DROP permalink, DROP description');
        $this->addSql('ALTER TABLE sylius_taxonomy DROP name');

        $this->addSql('ALTER TABLE sylius_product_attribute_translation ADD CONSTRAINT FK_93850EBA2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES sylius_product_attribute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_option_translation ADD CONSTRAINT FK_CBA491AD2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES sylius_product_option (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_country_translation ADD CONSTRAINT FK_B8BD1DDC2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES sylius_country (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_translation ADD CONSTRAINT FK_105A9082C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES sylius_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_shipping_method_translation ADD CONSTRAINT FK_2B37DB3D2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES sylius_shipping_method (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_taxonomy_translation ADD CONSTRAINT FK_9F3F90D92C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES sylius_taxonomy (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_taxon_translation ADD CONSTRAINT FK_1487DFCF2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES sylius_taxon (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->connection->executeQuery('ALTER TABLE sylius_country ADD name VARCHAR(255) NOT NULL');
        $countryTranslations = $this->connection->fetchAll('SELECT * FROM sylius_country_translation WHERE locale="' . $this->defaultLocale . '"');;
        foreach ($countryTranslations as $countryTranslation) {
            $this->connection->update(
                'sylius_country',
                array('name' => $countryTranslation['name']),
                array('id' => $countryTranslation['translatable_id'])
            );
        }

        $this->connection->executeQuery('ALTER TABLE sylius_product ADD name VARCHAR(255) NOT NULL, ADD slug VARCHAR(255) NOT NULL, ADD description LONGTEXT NOT NULL, ADD meta_keywords VARCHAR(255) DEFAULT NULL, ADD meta_description VARCHAR(255) DEFAULT NULL, ADD short_description VARCHAR(255) DEFAULT NULL');
        $productTranslations = $this->connection->fetchAll('SELECT * FROM sylius_product_translation WHERE locale="' . $this->defaultLocale . '"');
        foreach ($productTranslations as $productTranslation) {
            $this->connection->update(
                'sylius_product',
                array(
                    'name'              => $productTranslation['name'],
                    'slug'              => $productTranslation['slug'],
                    'description'       => $productTranslation['description'],
                    'meta_keywords'     => $productTranslation['meta_keywords'],
                    'meta_description'  => $productTranslation['meta_description'],
                    'short_description' => $productTranslation['short_description'],
                ),
                array('id' => $productTranslation['translatable_id'])
            );
        }
        $this->connection->executeQuery('CREATE UNIQUE INDEX UNIQ_677B9B74989D9B62 ON sylius_product (slug)');

        $this->connection->executeQuery('ALTER TABLE sylius_product_attribute ADD presentation VARCHAR(255) NOT NULL');
        $productAttributesTranslations = $this->connection->fetchAll('SELECT * FROM sylius_product_attribute_translation WHERE locale="' . $this->defaultLocale . '"');;
        foreach ($productAttributesTranslations as $productAttributesTranslation) {
            $this->connection->update(
                'sylius_product_attribute',
                array('presentation' => $productAttributesTranslation['presentation']),
                array('id' => $productAttributesTranslation['translatable_id'])
            );
        }

        $this->connection->executeQuery('ALTER TABLE sylius_product_option ADD presentation VARCHAR(255) NOT NULL');
        $productOptionTranslations = $this->connection->fetchAll('SELECT * FROM sylius_product_option_translation WHERE locale="' . $this->defaultLocale . '"');;
        foreach ($productOptionTranslations as $productOptionTranslation) {
            $this->connection->update(
                'sylius_product_option',
                array('presentation' => $productOptionTranslation['presentation']),
                array('id' => $productOptionTranslation['translatable_id'])
            );
        }

        $this->connection->executeQuery('ALTER TABLE sylius_shipping_method ADD name VARCHAR(255) NOT NULL');
        $shippingMethodsTranslations = $this->connection->fetchAll('SELECT * FROM sylius_shipping_method_translation WHERE locale="' . $this->defaultLocale . '"');;
        foreach ($shippingMethodsTranslations as $shippingMethodsTranslation) {
            $this->connection->update(
                'sylius_shipping_method',
                array('name' => $shippingMethodsTranslation['name']),
                array('id' => $shippingMethodsTranslation['translatable_id'])
            );
        }

        $this->connection->executeQuery('ALTER TABLE sylius_taxonomy ADD name VARCHAR(255) NOT NULL');
        $taxonomyTranslations = $this->connection->fetchAll('SELECT * FROM sylius_taxonomy_translation WHERE locale="' . $this->defaultLocale . '"');;
        foreach ($taxonomyTranslations as $taxonomyTranslation) {
            $this->connection->update(
                'sylius_taxonomy',
                array('name' => $taxonomyTranslation['name']),
                array('id' => $taxonomyTranslation['translatable_id'])
            );
        }

        $this->connection->executeQuery('ALTER TABLE sylius_taxon ADD name VARCHAR(255) NOT NULL, ADD slug VARCHAR(255) NOT NULL, ADD permalink VARCHAR(255) NOT NULL, ADD description LONGTEXT DEFAULT NULL');
        $taxonTranslations = $this->connection->fetchAll('SELECT * FROM sylius_taxon_translation WHERE locale="' . $this->defaultLocale . '"');;
        foreach ($taxonTranslations as $taxonTranslation) {
            $this->connection->update(
                'sylius_taxon',
                array('name'        => $taxonTranslation['name'],
                    'slug'        => $taxonTranslation['slug'],
                    'permalink'   => $taxonTranslation['permalink'],
                    'description' => $taxonTranslation['description']),
                array('id' => $taxonTranslation['translatable_id'])
            );
        }
        $this->connection->executeQuery('CREATE UNIQUE INDEX UNIQ_CFD811CA989D9B62 ON sylius_taxon (slug)');
        $this->connection->executeQuery('CREATE UNIQUE INDEX UNIQ_CFD811CAF286BC32 ON sylius_taxon (permalink)');

        $this->addSql('ALTER TABLE sylius_country_translation DROP FOREIGN KEY FK_B8BD1DDC2C2AC5D3');
        $this->addSql('ALTER TABLE sylius_product_attribute_translation DROP FOREIGN KEY FK_93850EBA2C2AC5D3');
        $this->addSql('ALTER TABLE sylius_product_option_translation DROP FOREIGN KEY FK_CBA491AD2C2AC5D3');
        $this->addSql('ALTER TABLE sylius_product_translation DROP FOREIGN KEY FK_105A9082C2AC5D3');
        $this->addSql('ALTER TABLE sylius_shipping_method_translation DROP FOREIGN KEY FK_2B37DB3D2C2AC5D3');
        $this->addSql('ALTER TABLE sylius_taxon_translation DROP FOREIGN KEY FK_1487DFCF2C2AC5D3');
        $this->addSql('ALTER TABLE sylius_taxonomy_translation DROP FOREIGN KEY FK_9F3F90D92C2AC5D3');

        $this->addSql('DROP TABLE sylius_product_attribute_translation');
        $this->addSql('DROP TABLE sylius_product_option_translation');
        $this->addSql('DROP TABLE sylius_country_translation');
        $this->addSql('DROP TABLE sylius_product_translation');
        $this->addSql('DROP TABLE sylius_shipping_method_translation');
        $this->addSql('DROP TABLE sylius_taxonomy_translation');
        $this->addSql('DROP TABLE sylius_taxon_translation');
    }
}
