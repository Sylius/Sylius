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
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;

class RegisterFormTypeExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function isSupported($configure)
    {
        return $configure & AbstractResourceExtension::CONFIGURE_FORMS;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ContainerBuilder $container, array $configuration, array $context = array())
    {
        foreach ($configuration['classes'] as $model => $serviceClasses) {
            if (!isset($serviceClasses['form']) || !is_array($serviceClasses['form'])) {
                continue;
            }
            foreach ($serviceClasses['form'] as $name => $class) {
                $suffix = ($name === AbstractResourceExtension::DEFAULT_KEY ? '' : sprintf('_%s', $name));
                $alias = sprintf('%s_%s%s', $context['app_name'], $model, $suffix);
                $definition = new Definition($class);
                if ('choice' === $name) {
                    $definition->setArguments(array(
                        $serviceClasses['model'],
                        $configuration['driver'],
                        $alias,
                    ));
                } else {
                    $definition->setArguments(array(
                        $serviceClasses['model'],
                        new Parameter(sprintf('%s.validation_group.%s%s', $context['app_name'], $model, $suffix)),
                    ));
                }
                $definition->addTag('form.type', array('alias' => $alias));
                $container->setDefinition(
                    sprintf('%s.form.type.%s%s', $context['app_name'], $model, $suffix),
                    $definition
                );
            }
        }
    }
}