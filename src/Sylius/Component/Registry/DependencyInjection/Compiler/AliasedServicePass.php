<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sylius\Component\Registry\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * This compiler pass will add services to a registered ServiceRegistry
 * definition.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
final class AliasedServicePass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private $serviceRegistryId;

    /**
     * @var string
     */
    private $serviceTag;

    /**
     * @var string
     */
    private $aliasAttribute;

    /**
     * @param string $serviceRegistryId
     * @param string $serviceTag
     * @param array $options
     */
    public function __construct(
        $serviceRegistryId,
        $serviceTag,
        $aliasAttribute = 'alias'
    ) {
        $this->serviceRegistryId = $serviceRegistryId;
        $this->serviceTag = $serviceTag;
        $this->aliasAttribute = $aliasAttribute;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->serviceRegistryId)) {
            return;
        }

        $registry = $container->findDefinition($this->serviceRegistryId);
        $registryClass = $container->getParameterBag()->resolveValue($registry->getClass());
        $reflection = new \ReflectionClass($registryClass);

        if (!$reflection->implementsInterface(ServiceRegistryInterface::class)) {
            throw new InvalidArgumentException(sprintf(
                'The service registry "%s" must implement the "%s" interface.',
                $this->serviceRegistryId, ServiceRegistryInterface::class
            ));
        }

        foreach ($container->findTaggedServiceIds($this->serviceTag) as $id => $attributes) {
            if (!isset($attributes[0][$this->aliasAttribute]))  {
                throw new InvalidArgumentException(sprintf(
                    'Service "%s" with tag "%s" needs to have the "%s" attribute.',
                    $id, $this->serviceTag, $this->aliasAttribute
                ));
            }

            $registry->addMethodCall('register', [$attributes[0][$this->aliasAttribute], new Reference($id)]);
        }
    }
}
