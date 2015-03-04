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

        $container
            ->findDefinition('sylius.event_subscriber.affiliate.load_metadata')
            ->replaceArgument(0, $config['classes']['affiliate']['model'])
            ->replaceArgument(1, $config['classes']['affiliate']['referral'])
        ;

        unset($config['classes']['affiliate']['referral']);

        if ($config['referral_listener']['enabled']) {
            $container
                ->findDefinition('sylius.listener.referral')
                ->replaceArgument(2, $config['referral_listener']['query_parameter'])
                ->replaceArgument(3, $config['referral_listener']['cookie_name'])
                ->replaceArgument(4, $config['referral_listener']['cookie_lifetime'])
            ;
        } else {
            $container->removeDefinition('sylius.listener.referral');
        }
    }
}
