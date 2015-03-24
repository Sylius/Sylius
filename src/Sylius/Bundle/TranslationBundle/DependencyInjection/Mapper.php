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
        if (!$container->hasParameter('sylius.translation.default_mapping')) {
            throw new \Exception('Missing parameter sylius.translation.default_mapping. Default translation mapping must be defined!');
        }

        $defaultMapping = $container->getParameter('sylius.translation.default_mapping');
        $mapping = array();

        $mapping[$config['model']] = $config;
        $mapping[$config['model']]['translation']['mapping'] += $defaultMapping;

        $mapping[$config['translation']['model']] = $mapping[$config['model']];

        if ($container->hasParameter('sylius.translation.mapping')) {
            $mapping = array_merge($mapping, $container->getParameter('sylius.translation.mapping'));
        }

        $container->setParameter('sylius.translation.mapping', $mapping);
    }
}