<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
abstract class AbstractRegisterServicePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->getRegistryIdentifier())) {
            return;
        }

        $registry = $container->getDefinition($this->getRegistryIdentifier());

        foreach ($container->findTaggedServiceIds($this->getTagName()) as $id => $attributes) {
            $identifier = $this->getIdentifierAttribute();
            if (!isset($attributes[0]['type'])) {
                throw new \InvalidArgumentException(
                    sprintf('Tagged service needs to have `%s` attribute.', $identifier)
                );
            }

            $registry->addMethodCall('register', array($attributes[0][$identifier], new Reference($id)));
        }
    }

    /**
     * @return string
     */
    abstract protected function getRegistryIdentifier();

    /**
     * @return string
     */
    abstract protected function getTagName();

    /**
     * @return string
     */
    abstract protected function getIdentifierAttribute();
}