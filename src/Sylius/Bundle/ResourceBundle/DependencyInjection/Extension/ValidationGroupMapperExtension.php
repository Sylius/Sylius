<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Extension;

use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ValidationGroupMapperExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function isSupported($configure)
    {
        return $configure & AbstractResourceExtension::CONFIGURE_VALIDATORS;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ContainerBuilder $container, array $configuration, array $context = array())
    {
        if (isset($configuration['validation_groups'])) {
            return;
        }

        foreach ($configuration['validation_groups'] as $model => $groups) {
            $container->setParameter(sprintf('%s.validation_group.%s', $context['app_name'], $model), $groups);
        }
    }
}