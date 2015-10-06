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

/**
 * AffiliateGoal extension.
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
        $config = $this->configure(
            $config,
            new Configuration(),
            $container,
            self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS
        );

        $container->setParameter('sylius_affiliate.referral.query_parameter', $config['referral']['query_parameter']);
        $container->setParameter('sylius_affiliate.referral.cookie_name', $config['referral']['cookie_name']);
        $container->setParameter('sylius_affiliate.referral.cookie_lifetime', $config['referral']['cookie_lifetime']);

        if (!$config['referral']['enabled']) {
            $container->removeDefinition('sylius.listener.referral');
        }
    }
}
