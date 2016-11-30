<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161102145826 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     */
    public function postUp(Schema $schema)
    {
        $rootNodes = $this->container->get('sylius.repository.taxon')->findRootNodes();
        $this->updatePosition($rootNodes);
        $this->container->get('sylius.manager.taxon')->flush();
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_taxon ADD position INT NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_taxon DROP position');
    }

    /**
     * @param mixed $rootNodes
     */
    private function updatePosition($rootNodes)
    {
        /** @var TaxonInterface $rootNode */
        foreach ($rootNodes as $key => $rootNode) {
            $rootNode->setPosition($key);
            if (!$rootNode->getChildren()->isEmpty()) {
                $this->updatePosition($rootNode->getChildren());
            }
        }
    }
}
