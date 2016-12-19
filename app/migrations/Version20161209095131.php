<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Sylius\Component\Addressing\Model\Scope;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161209095131 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

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

        $zoneRepository = $this->container->get('sylius.repository.zone');
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');

        /** @var ZoneInterface $zone */
        foreach ($zoneRepository->findBy(['scope' => null]) as $zone) {
            $zone->setScope(Scope::ALL);
        }

        $entityManager->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

        $zoneRepository = $this->container->get('sylius.repository.zone');
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');

        /** @var ZoneInterface $zone */
        foreach ($zoneRepository->findBy(['scope' => Scope::ALL]) as $zone) {
            $zone->setScope('');
        }

        $entityManager->flush();
    }
}
