<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Sylius\Component\Addressing\Model\Scope;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20161209095131 extends AbstractMigration implements ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema): void
    {
        $zoneRepository = $this->container->get('sylius.repository.zone');
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');

        /** @var ZoneInterface $zone */
        foreach ($zoneRepository->findBy(['scope' => null]) as $zone) {
            $zone->setScope(Scope::ALL);
        }

        $entityManager->flush();
    }

    public function down(Schema $schema): void
    {
        $zoneRepository = $this->container->get('sylius.repository.zone');
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');

        /** @var ZoneInterface $zone */
        foreach ($zoneRepository->findBy(['scope' => Scope::ALL]) as $zone) {
            $zone->setScope('');
        }

        $entityManager->flush();
    }
}
