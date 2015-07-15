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

/**
 * This mapper is responsible for defining translations mapping.
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Mapper
{
    /**
     * {@inheritdoc}
     */
    public function mapTranslations(array $config, ContainerBuilder $container)
    {
        $mapping = array();

        $mapping[$config['model']] = $config;
        $mapping[$config['translation']['model']] = $mapping[$config['model']];

        if ($container->hasParameter('sylius.translation.mapping')) {
            $mapping = array_merge($mapping, $container->getParameter('sylius.translation.mapping'));
        }

        $container->setParameter('sylius.translation.mapping', $mapping);
    }
}