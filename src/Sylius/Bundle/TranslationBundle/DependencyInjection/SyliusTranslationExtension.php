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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceExtension;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class SyliusTranslationExtension extends AbstractResourceExtension
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
            self::CONFIGURE_LOADER | self::CONFIGURE_PARAMETERS | self::CONFIGURE_DATABASE
        );

        $container->setParameter('sylius.translation.default_mapping', $config['mapping']);
        $container->setAlias('sylius.translation.locale_provider', $config['locale_provider']);
    }
}
