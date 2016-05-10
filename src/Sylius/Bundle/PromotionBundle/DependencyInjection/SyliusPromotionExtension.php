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
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class SyliusPromotionExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->mapFormValidationGroupsParameters($config, $container);

        $configFiles = [
            'services.xml',
            sprintf('driver/%s.xml', $config['driver']),
        ];

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);
        $this->overwriteCouponFactory($container);
        $this->overwriteActionFactory($container);

        $container
            ->getDefinition('sylius.form.type.promotion_action')
            ->replaceArgument(1, new Reference('sylius.registry.promotion_action'))
        ;
        $container
            ->getDefinition('sylius.form.type.promotion_rule')
            ->replaceArgument(1, new Reference('sylius.registry.promotion_rule_checker'))
        ;
    }

    /**
     * @param ContainerBuilder $container
     */
    private function overwriteCouponFactory(ContainerBuilder $container)
    {
        $couponFactoryDefinition = $container->getDefinition('sylius.factory.promotion_coupon');
        $couponFactoryClass = $couponFactoryDefinition->getClass();
        $couponFactoryDefinition->setClass(Factory::class);

        $decoratedCouponFactoryDefinition = new Definition($couponFactoryClass);
        $decoratedCouponFactoryDefinition
            ->addArgument($couponFactoryDefinition)
            ->addArgument(new Reference('sylius.repository.promotion'))
        ;
        $container->setDefinition('sylius.factory.promotion_coupon', $decoratedCouponFactoryDefinition);
    }


    /**
     * @param ContainerBuilder $container
     */
    private function overwriteActionFactory(ContainerBuilder $container)
    {
        $baseFactoryDefinition = new Definition(Factory::class, [new Parameter('sylius.model.promotion_action.class')]);
        $promotionActionFactoryClass = $container->getParameter('sylius.factory.promotion_action.class');
        $decoratedPromotionActionFactoryDefinition = new Definition($promotionActionFactoryClass, [$baseFactoryDefinition]);

        $container->setDefinition('sylius.factory.promotion_action', $decoratedPromotionActionFactoryDefinition);
    }
}
