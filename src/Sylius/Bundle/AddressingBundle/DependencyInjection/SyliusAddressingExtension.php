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

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class SyliusAddressingExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load(sprintf('driver/%s.xml', $config['driver']));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $configFiles = [
            'services.xml',
            'twig.xml',
        ];

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $container->setParameter('sylius.scope.zone', $config['scopes']);

        $container
            ->getDefinition('sylius.form.type.province_choice')
            ->setArguments([
                new Reference('sylius.repository.province'),
            ])
        ;

        $container
            ->getDefinition('sylius.form.type.province_code_choice')
            ->setArguments([
                new Reference('sylius.repository.province'),
            ])
        ;

        $container
            ->getDefinition('sylius.form.type.country_choice')
            ->setArguments([
                new Reference('sylius.repository.country'),
            ])
        ;

        $container
            ->getDefinition('sylius.form.type.country_code_choice')
            ->setArguments([
                new Reference('sylius.repository.country'),
            ])
        ;

        $container
            ->getDefinition('sylius.form.type.zone_code_choice')
            ->setArguments([
                new Reference('sylius.repository.zone'),
            ])
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
