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

namespace Sylius\Bundle\CurrencyBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Component\Currency\Attribute\AsCurrencyContext;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusCurrencyExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load(sprintf('services/integrations/%s.xml', $config['driver']));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $loader->load('services.xml');

        $container->registerForAutoconfiguration(CurrencyContextInterface::class)
            ->addTag('sylius.context.currency')
        ;

        $container->registerAttributeForAutoconfiguration(
            AsCurrencyContext::class,
            static function (ChildDefinition $definition, AsCurrencyContext $attribute) {
                $definition->addTag('sylius.context.currency', [
                    'priority' => $attribute->priority,
                ]);
            }
        );
    }
}
