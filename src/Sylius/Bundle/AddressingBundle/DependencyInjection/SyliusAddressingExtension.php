<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Parameter;
/**
 * Addressing extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class SyliusAddressingExtension extends AbstractResourceExtension
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
            self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS
            | self::CONFIGURE_FORMS
        );

        $container->setParameter('sylius.scope.zone', $config['scopes']);

        $container
            ->getDefinition('sylius.form.type.province_choice')
            ->setArguments(array(
                new Reference('sylius.repository.province')
            ))
        ;

        $container
            ->getDefinition('sylius.form.type.province_choice')
            ->setArguments(array(
                new Reference('sylius.repository.province')
            ))
        ;

        $container
            ->getDefinition('sylius.form.type.address')
            ->addArgument(new Reference('sylius.form.listener.address'))
        ;

        $container
            ->getDefinition('sylius.form.type.zone')
            ->addArgument(new Parameter('sylius.scope.zone'))
        ;
    }
}
