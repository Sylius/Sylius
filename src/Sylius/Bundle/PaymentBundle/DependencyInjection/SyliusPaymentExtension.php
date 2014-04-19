<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PaymentBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * Payments extension.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusPaymentExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        list ($config) = $this->configure(
            $config,
            new Configuration(),
            $container,
            self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS
        );

        $container->setParameter('sylius.payment_gateways', $this->getEnabledGateways($config));
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        if (!$container->hasExtension('sylius_payum')) {
            throw new \RuntimeException('"SyliusPayumBundle" must be registered in kernel.');
        }

        $config = current($container->getExtensionConfig($this->getAlias()));

        $container->prependExtensionConfig('sylius_payum', array(
            'driver'   => isset($config['driver']) ? $config['driver'] : 'doctrine/orm',
            'gateways' => $this->getEnabledGateways($config),
        ));
    }

    private function getEnabledGateways(array $config)
    {
        $gateways = array();
        foreach (array_keys($config['gateways']) as $gateway) {
            $gateways[$gateway] = true;
        }

        return $gateways;
    }
}
