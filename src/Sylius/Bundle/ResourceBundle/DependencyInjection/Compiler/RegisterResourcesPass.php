<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler;

use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class RegisterResourcesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        try {
            $resources = $container->getParameter('sylius.resources');
            $registry = $container->findDefinition('sylius.resource_registry');
        } catch (InvalidArgumentException $exception) {
            return;
        }

        foreach ($resources as $alias => $configuration) {
            $this->validateSyliusResource($configuration['classes']['model']);
            $registry->addMethodCall('addFromAliasAndConfiguration', [$alias, $configuration]);
        }
    }

    /**
     * @param string $class
     */
    private function validateSyliusResource($class)
    {
        if (!in_array(ResourceInterface::class, class_implements($class), true)) {
            throw new InvalidArgumentException(sprintf(
                'Class "%s" must implement "%s" to be registered as a Sylius resource.',
                $class,
                ResourceInterface::class
            ));
        }
    }
}
