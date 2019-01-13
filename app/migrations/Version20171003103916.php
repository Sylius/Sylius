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
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20171003103916 extends AbstractMigration implements ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    public function setContainer(?ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $defaultLocale = $this->container->getParameter('locale');
        $productAttributeRepository = $this->container->get('sylius.repository.product_attribute');

        $productAttributes = $productAttributeRepository->findBy(['type' => SelectAttributeType::TYPE]);
        /** @var ProductAttributeInterface $productAttribute */
        foreach ($productAttributes as $productAttribute) {
            $configuration = $productAttribute->getConfiguration();
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
                'id' => $productAttribute->getId(),
                'configuration' => serialize($upgradedConfiguration),
            ]);
        }
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $defaultLocale = $this->container->getParameter('locale');
        $productAttributeRepository = $this->container->get('sylius.repository.product_attribute');

        $productAttributes = $productAttributeRepository->findBy(['type' => SelectAttributeType::TYPE]);
        /** @var ProductAttributeInterface $productAttribute */
        foreach ($productAttributes as $productAttribute) {
            $configuration = $productAttribute->getConfiguration();
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
                'id' => $productAttribute->getId(),
                'configuration' => serialize($downgradedConfiguration),
            ]);
        }
    }
}
