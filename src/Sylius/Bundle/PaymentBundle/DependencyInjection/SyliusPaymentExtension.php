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

/**
 * Sylius payments component extension.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusPaymentExtension extends AbstractResourceExtension
{
    protected $classesToCompile = array(
        'Sylius\Component\Payment\PaymentTransitions',
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        list($config) = $this->configure($config, new Configuration(), $container, self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS);

        $container->setParameter('sylius.payment_gateways', $config['gateways']);
    }
}
