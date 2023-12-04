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

namespace Sylius\Bundle\ProductBundle\DependencyInjection;

use Sylius\Bundle\ProductBundle\Attribute\AsProductVariantResolver;
use Sylius\Bundle\ProductBundle\Controller\ProductAttributeController;
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductAttributeValueRepository;
use Sylius\Bundle\ProductBundle\Form\Type\ProductAttributeTranslationType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductAttributeType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductAttributeValueType;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Component\Product\Model\ProductAttribute;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeTranslation;
use Sylius\Component\Product\Model\ProductAttributeTranslationInterface;
use Sylius\Component\Product\Model\ProductAttributeValue;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusProductExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load(sprintf('services/integrations/%s.xml', $config['driver']));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $loader->load('services.xml');

        $this->registerAutoconfiguration($container);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $container->getExtensionConfig($this->getAlias()));

        $this->prependAttribute($container, $config);
    }

    private function registerAutoconfiguration(ContainerBuilder $container): void
    {
        $container->registerAttributeForAutoconfiguration(
            AsProductVariantResolver::class,
            static function (ChildDefinition $definition, AsProductVariantResolver $attribute): void {
                $definition->addTag(AsProductVariantResolver::SERVICE_TAG, ['priority' => $attribute->getPriority()]);
            },
        );
    }

    private function prependAttribute(ContainerBuilder $container, array $config): void
    {
        if (!$container->hasExtension('sylius_attribute')) {
            return;
        }

        $container->prependExtensionConfig('sylius_attribute', [
            'resources' => [
                'product' => [
                    'subject' => $config['resources']['product']['classes']['model'],
                    'attribute' => [
                        'classes' => [
                            'model' => ProductAttribute::class,
                            'interface' => ProductAttributeInterface::class,
                            'controller' => ProductAttributeController::class,
                            'form' => ProductAttributeType::class,
                        ],
                        'translation' => [
                            'classes' => [
                                'model' => ProductAttributeTranslation::class,
                                'interface' => ProductAttributeTranslationInterface::class,
                                'form' => ProductAttributeTranslationType::class,
                            ],
                        ],
                    ],
                    'attribute_value' => [
                        'classes' => [
                            'model' => ProductAttributeValue::class,
                            'interface' => ProductAttributeValueInterface::class,
                            'repository' => ProductAttributeValueRepository::class,
                            'form' => ProductAttributeValueType::class,
                        ],
                    ],
                ],
            ],
        ]);
    }
}
