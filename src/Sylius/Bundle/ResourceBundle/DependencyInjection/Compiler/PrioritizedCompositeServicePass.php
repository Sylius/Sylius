<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

abstract class PrioritizedCompositeServicePass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private $serviceId;

    /**
     * @var string
     */
    private $compositeId;

    /**
     * @var string
     */
    private $tagName;

    /**
     * @var string
     */
    private $methodName;

    /**
     * @param string $serviceId
     * @param string $compositeId
     * @param string $tagName
     * @param string $methodName
     */
    public function __construct(string $serviceId, string $compositeId, string $tagName, string $methodName)
    {
        $this->serviceId = $serviceId;
        $this->compositeId = $compositeId;
        $this->tagName = $tagName;
        $this->methodName = $methodName;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition($this->compositeId)) {
            return;
        }

        $this->injectTaggedServicesIntoComposite($container);
        $this->addAliasForCompositeIfServiceDoesNotExist($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function injectTaggedServicesIntoComposite(ContainerBuilder $container): void
    {
        $contextDefinition = $container->findDefinition($this->compositeId);

        $taggedServices = $container->findTaggedServiceIds($this->tagName);
        foreach ($taggedServices as $id => $tags) {
            $this->addMethodCalls($contextDefinition, $id, $tags);
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    private function addAliasForCompositeIfServiceDoesNotExist(ContainerBuilder $container): void
    {
        if ($container->has($this->serviceId)) {
            return;
        }

        $container->setAlias($this->serviceId, $this->compositeId)->setPublic(true);
    }

    /**
     * @param Definition $contextDefinition
     * @param string $id
     * @param array $tags
     */
    private function addMethodCalls(Definition $contextDefinition, string $id, array $tags): void
    {
        foreach ($tags as $attributes) {
            $this->addMethodCall($contextDefinition, $id, $attributes);
        }
    }

    /**
     * @param Definition $contextDefinition
     * @param string $id
     * @param array $attributes
     */
    private function addMethodCall(Definition $contextDefinition, string $id, array $attributes): void
    {
        $arguments = [new Reference($id)];

        if (isset($attributes['priority'])) {
            $arguments[] = $attributes['priority'];
        }

        $contextDefinition->addMethodCall($this->methodName, $arguments);
    }
}
