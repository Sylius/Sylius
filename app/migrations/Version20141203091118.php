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
        $this->container     = $container;
        $this->defaultLocale = $container->getParameter('sylius.locale');
    }

    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->connection->executeQuery('CREATE TABLE sylius_product_attribute_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, presentation VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_93850EBA2C2AC5D3 (translatable_id), UNIQUE INDEX sylius_product_attribute_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $product_attributes = $this->connection->fetchAll('SELECT * FROM sylius_product_attribute');
        foreach ($product_attributes as $product_attribute) {
            $this->connection->insert('sylius_product_attribute_translation', array(
                'presentation'    => $product_attribute['presentation'],
                'translatable_id' => $product_attribute['id'],
                'locale'          => $this->defaultLocale
            ));
        }

        $this->connection->executeQuery('CREATE TABLE sylius_product_option_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, presentation VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_CBA491AD2C2AC5D3 (translatable_id), UNIQUE INDEX sylius_product_option_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $product_options = $this->connection->fetchAll('SELECT * FROM sylius_product_option');
        foreach ($product_options as $product_option) {
            $this->connection->insert('sylius_product_option_translation', array(
                'presentation'    => $product_option['presentation'],
                'translatable_id' => $product_option['id'],
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
        $sylius_products = $this->connection->fetchAll('SELECT * FROM sylius_product');
        foreach ($sylius_products as $sylius_product) {
            $this->connection->insert('sylius_product_translation', array(
                'name'              => $sylius_product['name'],
                'slug'              => $sylius_product['slug'],
                'description'       => $sylius_product['description'],
                'meta_keywords'     => $sylius_product['meta_keywords'],
                'meta_description'  => $sylius_product['meta_description'],
                'short_description' => $sylius_product['short_description'],
                'translatable_id'   => $sylius_product['id'],
                'locale'            => $this->defaultLocale
            ));
        }

        $this->connection->executeQuery('CREATE TABLE sylius_shipping_method_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_2B37DB3D2C2AC5D3 (translatable_id), UNIQUE INDEX sylius_shipping_method_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $shipping_methods = $this->connection->fetchAll('SELECT * FROM sylius_shipping_method');
        foreach ($shipping_methods as $shipping_method) {
            $this->connection->insert('sylius_shipping_method_translation', array(
                'name'            => $shipping_method['name'],
                'translatable_id' => $shipping_method['id'],
                'locale'          => $this->defaultLocale
            ));
        }

        $this->connection->executeQuery('CREATE TABLE sylius_taxonomy_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_9F3F90D92C2AC5D3 (translatable_id), UNIQUE INDEX sylius_taxonomy_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $sylius_taxonomys = $this->connection->fetchAll('SELECT * FROM sylius_taxonomy');
        foreach ($sylius_taxonomys as $sylius_taxonomy) {
            $this->connection->insert('sylius_taxonomy_translation', array(
                'name'            => $sylius_taxonomy['name'],
                'translatable_id' => $sylius_taxonomy['id'],
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
        $country_translations = $this->connection->fetchAll('SELECT * FROM sylius_country_translation WHERE locale="' . $this->defaultLocale . '"');;
        foreach ($country_translations as $country_translation) {
            $this->connection->update(
                'sylius_country',
                array('name' => $country_translation['name']),
                array('id' => $country_translation['translatable_id'])
            );
        }

        $this->connection->executeQuery('ALTER TABLE sylius_product ADD name VARCHAR(255) NOT NULL, ADD slug VARCHAR(255) NOT NULL, ADD description LONGTEXT NOT NULL, ADD meta_keywords VARCHAR(255) DEFAULT NULL, ADD meta_description VARCHAR(255) DEFAULT NULL, ADD short_description VARCHAR(255) DEFAULT NULL');
        $product_translations = $this->connection->fetchAll('SELECT * FROM sylius_product_translation WHERE locale="' . $this->defaultLocale . '"');
        foreach ($product_translations as $product_translation) {
            $this->connection->update(
                'sylius_product',
                array(
                    'name'              => $product_translation['name'],
                    'slug'              => $product_translation['slug'],
                    'description'       => $product_translation['description'],
                    'meta_keywords'     => $product_translation['meta_keywords'],
                    'meta_description'  => $product_translation['meta_description'],
                    'short_description' => $product_translation['short_description'],
                ),
                array('id' => $product_translation['translatable_id'])
            );
        }
        $this->connection->executeQuery('CREATE UNIQUE INDEX UNIQ_677B9B74989D9B62 ON sylius_product (slug)');

        $this->connection->executeQuery('ALTER TABLE sylius_product_attribute ADD presentation VARCHAR(255) NOT NULL');
        $product_attributes_translations = $this->connection->fetchAll('SELECT * FROM sylius_product_attribute_translation WHERE locale="' . $this->defaultLocale . '"');;
        foreach ($product_attributes_translations as $product_attributes_translation) {
            $this->connection->update(
                'sylius_product_attribute',
                array('presentation' => $product_attributes_translation['presentation']),
                array('id' => $product_attributes_translation['translatable_id'])
            );
        }

        $this->connection->executeQuery('ALTER TABLE sylius_product_option ADD presentation VARCHAR(255) NOT NULL');
        $product_option_translations = $this->connection->fetchAll('SELECT * FROM sylius_product_option_translation WHERE locale="' . $this->defaultLocale . '"');;
        foreach ($product_option_translations as $product_option_translation) {
            $this->connection->update(
                'sylius_product_option',
                array('presentation' => $product_option_translation['presentation']),
                array('id' => $product_option_translation['translatable_id'])
            );
        }

        $this->connection->executeQuery('ALTER TABLE sylius_shipping_method ADD name VARCHAR(255) NOT NULL');
        $shipping_methods_translations = $this->connection->fetchAll('SELECT * FROM sylius_shipping_method_translation WHERE locale="' . $this->defaultLocale . '"');;
        foreach ($shipping_methods_translations as $shipping_methods_translation) {
            $this->connection->update(
                'sylius_shipping_method',
                array('name' => $shipping_methods_translation['name']),
                array('id' => $shipping_methods_translation['translatable_id'])
            );
        }

        $this->connection->executeQuery('ALTER TABLE sylius_taxonomy ADD name VARCHAR(255) NOT NULL');
        $taxonomy_translations = $this->connection->fetchAll('SELECT * FROM sylius_taxonomy_translation WHERE locale="' . $this->defaultLocale . '"');;
        foreach ($taxonomy_translations as $taxonomy_translation) {
            $this->connection->update(
                'sylius_taxonomy',
                array('name' => $taxonomy_translation['name']),
                array('id' => $taxonomy_translation['translatable_id'])
            );
        }

        $this->connection->executeQuery('ALTER TABLE sylius_taxon ADD name VARCHAR(255) NOT NULL, ADD slug VARCHAR(255) NOT NULL, ADD permalink VARCHAR(255) NOT NULL, ADD description LONGTEXT DEFAULT NULL');
        $taxon_translations = $this->connection->fetchAll('SELECT * FROM sylius_taxon_translation WHERE locale="' . $this->defaultLocale . '"');;
        foreach ($taxon_translations as $taxon_translation) {
            $this->connection->update(
                'sylius_taxon',
                array('name'        => $taxon_translation['name'],
                      'slug'        => $taxon_translation['slug'],
                      'permalink'   => $taxon_translation['permalink'],
                      'description' => $taxon_translation['description']),
                array('id' => $taxon_translation['translatable_id'])
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
