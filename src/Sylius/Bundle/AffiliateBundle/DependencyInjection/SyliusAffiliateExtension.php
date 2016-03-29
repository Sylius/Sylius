<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AffiliateBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Affiliate extension.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class SyliusAffiliateExtension extends AbstractResourceExtension
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

        $configFiles = [
            'services.xml',
        ];

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $container->setParameter('sylius_affiliate.referral.query_parameter', $config['referral']['query_parameter']);
        $container->setParameter('sylius_affiliate.referral.cookie_name', $config['referral']['cookie_name']);
        $container->setParameter('sylius_affiliate.referral.cookie_lifetime', $config['referral']['cookie_lifetime']);

        if (!$config['referral']['enabled']) {
            $container->removeDefinition('sylius.listener.referral');
        }

        $container
            ->getDefinition('sylius.form.type.affiliate_rule')
            ->replaceArgument(1, new Reference('sylius.registry.affiliate_goal_rule_checker'))
        ;
        $container
            ->getDefinition('sylius.form.type.affiliate_provision')
            ->replaceArgument(1, new Reference('sylius.registry.affiliate_goal_provision'))
        ;
    }
}
