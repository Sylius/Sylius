<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class SyliusUserExtension extends AbstractResourceExtension
{
    protected $configFiles = array(
        'services.xml',
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->configure(
            $config,
            new Configuration(),
            $container,
            self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS | self::CONFIGURE_FORMS
        );

        $container->setParameter('sylius.user.resetting.token_ttl', $config['resetting']['token']['ttl']);
        $container->setParameter('sylius.user.resetting.token_length', $config['resetting']['token']['length']);
        $container->setParameter('sylius.user.resetting.pin_length', $config['resetting']['pin']['length']);

        $container
            ->getDefinition('sylius.form.type.customer_registration')
            ->addArgument(new Reference('sylius.repository.customer'))
        ;
        $container
            ->getDefinition('sylius.form.type.customer_simple_registration')
            ->addArgument(new Reference('sylius.repository.customer'))
        ;
        $container
            ->getDefinition('sylius.form.type.customer_guest')
            ->addArgument(new Reference('sylius.repository.customer'))
        ;
    }
}
