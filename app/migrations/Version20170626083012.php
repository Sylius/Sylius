<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170626083012 extends AbstractMigration implements ContainerAwareInterface
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
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $defaultLocale = $this->container->getParameter('locale');
        $conn = $this->container->get('doctrine.dbal.default_connection');

        $attributes = $conn->executeQuery('SELECT id, configuration FROM sylius_product_attribute WHERE `type` = "select"')->fetchAll();
        foreach ($attributes as $attribute) {
            $updatedConfig = ['choices' => []];
            $configuration = unserialize($attribute['configuration']);

            foreach ($configuration as $configurationKey => $value) {
                if ($configurationKey === 'choices') {
                    foreach ($value as $key => $choice) {
                        $updatedConfig[$configurationKey][$key][$defaultLocale] = $choice;
                    }
                } else {
                    $updatedConfig[$configurationKey] = $value;
                }
            }

            $this->addSql('UPDATE sylius_product_attribute SET configuration = :configuration WHERE id = :id', [
                'id' => $attribute['id'],
                'configuration' => serialize($updatedConfig),
            ]);
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $defaultLocale = $this->container->getParameter('locale');
        $conn = $this->container->get('doctrine.dbal.default_connection');

        $attributes = $conn->executeQuery('SELECT id, configuration FROM sylius_product_attribute WHERE `type` = "select"')->fetchAll();
        foreach ($attributes as $attribute) {
            $newConfig = ['choices' => []];
            $configuration = unserialize($attribute['configuration']);

            foreach ($configuration as $configurationKey => $value) {
                if ($configurationKey === 'choices') {
                    foreach ($value as $key => $choice) {
                        foreach ($choice as $locale => $translate) {
                            if ($locale === $defaultLocale) {
                                $newConfig[$configurationKey][$key] = $translate;
                            }
                        }
                    }
                } else {
                    $newConfig[$configurationKey] = $value;
                }
            }

            $this->addSql('UPDATE sylius_product_attribute SET configuration = :configuration WHERE id = :id', [
                'id' => $attribute['id'],
                'configuration' => serialize($newConfig),
            ]);
        }
    }
}
