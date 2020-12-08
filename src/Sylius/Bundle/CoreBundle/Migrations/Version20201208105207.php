<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201208105207 extends AbstractMigration implements ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $adjustments = $this->getAdjustments();

        // this up() migration is auto-generated, please modify it to your needs

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }

    private function getAdjustments(): array
    {
        $adjustmentClass = $this->container->getParameter('sylius.model.adjustment.class');

        $entityManager = $this->getEntityManager($adjustmentClass);

        return $entityManager->createQueryBuilder()
            ->select('o')
            ->from($adjustmentClass, 'o')
            ->getQuery()
            ->getArrayResult()
            ;
    }

    private function getEntityManager(string $class): EntityManagerInterface
    {
        /** @var ManagerRegistry $managerRegistry */
        $managerRegistry = $this->container->get('doctrine');

        /** @var EntityManagerInterface $manager */
        $manager = $managerRegistry->getManagerForClass($class);

        return $manager;
    }
}
