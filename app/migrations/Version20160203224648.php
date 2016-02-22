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
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160203224648 extends AbstractMigration implements ContainerAwareInterface
{
    private $container;
    private $defaultLocale;

    public function setContainer(ContainerInterface $container = null)
    {
        if (null !== $container) {
            $this->container = $container;
            $this->defaultLocale = $container->getParameter('locale');
        }
    }
    
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->connection->executeQuery('CREATE TABLE sylius_product_option_value_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, presentation VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_8D4382DC2C2AC5D3 (translatable_id), UNIQUE INDEX sylius_product_option_value_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $productOptionValues = $this->connection->fetchAll('SELECT * FROM sylius_product_option_value');
        foreach ($productOptionValues as $productOptionValue) {
            $this->connection->insert('sylius_product_option_value_translation', [
                'presentation' => $productOptionValue['value'],
                'translatable_id' => $productOptionValue['id'],
                'locale' => $this->defaultLocale,
            ]);
        }
        $this->addSql('ALTER TABLE sylius_product_option_value_translation ADD CONSTRAINT FK_8D4382DC2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES sylius_product_option_value (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_option_value DROP value');
        $this->addSql('DROP INDEX fulltext_search_idx ON sylius_search_index');
        $this->addSql('CREATE INDEX fulltext_search_idx ON sylius_search_index (item_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->connection->executeQuery('ALTER TABLE sylius_product_option_value ADD value VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $productOptionValueTranslations = $this->connection->fetchAll('SELECT * FROM sylius_product_option_value_translation WHERE locale="'.$this->defaultLocale.'"');
        foreach ($productOptionValueTranslations as $productOptionValueTranslation) {
            $this->connection->update(
                'sylius_product_option_value',
                ['value' => $productOptionValueTranslation['presentation']],
                ['id' => $productOptionValueTranslation['translatable_id']]
            );
        }
        
        $this->addSql('DROP TABLE sylius_product_option_value_translation');
        $this->addSql('DROP INDEX fulltext_search_idx ON sylius_search_index');
        $this->addSql('CREATE FULLTEXT INDEX fulltext_search_idx ON sylius_search_index (value)');
    }
}
