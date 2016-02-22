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
class Version20150127131103 extends AbstractMigration implements ContainerAwareInterface
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

    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->connection->executeQuery('CREATE TABLE sylius_product_archetype_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_E0BA36D2C2AC5D3 (translatable_id), UNIQUE INDEX sylius_product_archetype_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->connection->executeQuery('ALTER TABLE sylius_product_archetype_translation ADD CONSTRAINT FK_E0BA36D2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES sylius_product_archetype (id) ON DELETE CASCADE');

        $archetypes = $this->connection->fetchAll('SELECT * FROM sylius_product_archetype');
        foreach ($archetypes as $archetype) {
            $this->connection->insert('sylius_product_archetype_translation', [
                'name' => $archetype['name'],
                'translatable_id' => $archetype['id'],
                'locale' => $this->defaultLocale,
            ]);
        }

        $this->addSql('ALTER TABLE sylius_product_archetype DROP name');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->connection->executeQuery('ALTER TABLE sylius_product_archetype ADD name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');

        $archetypeTranslations = $this->connection->fetchAll('SELECT * FROM sylius_product_archetype_translation WHERE locale="'.$this->defaultLocale.'"');
        foreach ($archetypeTranslations as $archetypeTranslation) {
            $this->connection->update(
                'sylius_product_archetype',
                ['name' => $archetypeTranslation['name']],
                ['id' => $archetypeTranslation['translatable_id']]
            );
        }

        $this->addSql('DROP TABLE sylius_product_archetype_translation');
    }
}
