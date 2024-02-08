<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20171003103916 extends AbstractMigration implements ContainerAwareInterface
{
    private ?ContainerInterface $container = null;

    public function setContainer(?ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function up(Schema $schema): void
    {
        $defaultLocale = $this->container->getParameter('locale');
        $productAttributes = $this->getProductAttributes();

        foreach ($productAttributes as $productAttribute) {
            $configuration = $productAttribute['configuration'];
            $upgradedConfiguration = [];

            foreach ($configuration as $configurationKey => $value) {
                if ('choices' === $configurationKey) {
                    foreach ($value as $key => $choice) {
                        $upgradedConfiguration[$configurationKey][$key][$defaultLocale] = $choice;
                    }

                    continue;
                }

                $upgradedConfiguration[$configurationKey] = $value;
            }

            $this->addSql('UPDATE sylius_product_attribute SET configuration = :configuration WHERE id = :id', [
                'id' => $productAttribute['id'],
                'configuration' => serialize($upgradedConfiguration),
            ]);
        }
    }

    public function down(Schema $schema): void
    {
        /** @var string $defaultLocale */
        $defaultLocale = $this->container->getParameter('locale');
        $productAttributes = $this->getProductAttributes();

        foreach ($productAttributes as $productAttribute) {
            $configuration = $productAttribute['configuration'];
            $downgradedConfiguration = [];

            foreach ($configuration as $configurationKey => $value) {
                if ('choices' === $configurationKey) {
                    foreach ($value as $key => $choice) {
                        if (array_key_exists($defaultLocale, $choice)) {
                            $downgradedConfiguration[$configurationKey][$key] = $choice[$defaultLocale];
                        }
                    }

                    continue;
                }

                $downgradedConfiguration[$configurationKey] = $value;
            }

            $this->addSql('UPDATE sylius_product_attribute SET configuration = :configuration WHERE id = :id', [
                'id' => $productAttribute['id'],
                'configuration' => serialize($downgradedConfiguration),
            ]);
        }
    }

    private function getProductAttributes(): array
    {
        /** @var string $productAttributeClass */
        $productAttributeClass = $this->container->getParameter('sylius.model.product_attribute.class');

        $entityManager = $this->getEntityManager($productAttributeClass);

        return $entityManager->createQueryBuilder()
            ->select('o.id, o.configuration')
            ->from($productAttributeClass, 'o')
            ->andWhere('o.type = :type')
            ->setParameter('type', SelectAttributeType::TYPE)
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
