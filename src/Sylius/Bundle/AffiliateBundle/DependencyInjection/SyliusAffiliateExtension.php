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

use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
        list($config) = $this->configure(
            $config,
            new Configuration(),
            $container,
            self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS
        );

        if ($config['referral_listener']['enabled']) {
            $container
                ->findDefinition('sylius.listener.referral')
                ->replaceArgument(1, $config['referral_listener']['query_parameter'])
                ->replaceArgument(2, $config['referral_listener']['cookie_name'])
                ->replaceArgument(3, $config['referral_listener']['cookie_lifetime'])
            ;
        } else {
            $container->removeDefinition('sylius.listener.referral');
        }
    }
}
