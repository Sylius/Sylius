<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Promotions extension.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class SyliusPromotionExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);
        $this->mapFormValidationGroupsParameters($config, $container);

        $configFiles = array(
            'services.xml',
        );

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $container
            ->getDefinition('sylius.form.type.promotion_action')
            ->replaceArgument(1, new Reference('sylius.registry.promotion_action'))
        ;
        $container
            ->getDefinition('sylius.form.type.promotion_rule')
            ->replaceArgument(1, new Reference('sylius.registry.promotion_rule_checker'))
        ;
    }
}
