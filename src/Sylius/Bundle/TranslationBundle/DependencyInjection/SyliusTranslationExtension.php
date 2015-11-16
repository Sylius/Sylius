<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TranslationBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class SyliusTranslationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $configFiles = array(
            'services.xml',
        );

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $container->setParameter('sylius.translation.default_locale', $config['default_locale']);
        $container->setAlias('sylius.translation.locale_provider', $config['locale_provider']);
    }
}

