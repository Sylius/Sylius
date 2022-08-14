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

namespace Sylius\Bundle\LocaleBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Component\Locale\Attribute\AsLocaleContext;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusLocaleExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $loader->load('services.xml');

        $container->setParameter('sylius_locale.locale', $config['locale']);

        $container->findDefinition('sylius.repository.locale')->setLazy(true);

        $container->registerForAutoconfiguration(LocaleContextInterface::class)
            ->addTag('sylius.context.locale')
        ;

        $container->registerAttributeForAutoconfiguration(
            AsLocaleContext::class,
            static function (ChildDefinition $definition, AsLocaleContext $attribute) {
                $definition->addTag('sylius.context.locale', [
                    'priority' => $attribute->priority,
                ]);
            }
        );
    }
}
