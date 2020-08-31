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

namespace Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Property\Factory;

use ApiPlatform\Core\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Property\PropertyMetadata;
use ApiPlatform\Core\Metadata\Property\SubresourceMetadata;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as SymfonyContainerInterface;

/** @internal */
final class ExtractorPropertyMetadataFactory implements PropertyMetadataFactoryInterface
{
    /** @var PropertyMetadataFactoryInterface */
    private $decoratedPropertyMetadataFactory;

    /** @var ContainerInterface */
    private $container;

    /** @var array */
    private $collectedParameters = [];

    public function __construct(PropertyMetadataFactoryInterface $decoratedPropertyMetadataFactory, ContainerInterface $container)
    {
        $this->decoratedPropertyMetadataFactory = $decoratedPropertyMetadataFactory;
        $this->container = $container;
    }

    public function create(string $resourceClass, string $property, array $options = []): PropertyMetadata
    {
        $propertyMetadata = $this->decoratedPropertyMetadataFactory->create($resourceClass, $property, $options);

        if (!$propertyMetadata->hasSubresource()) {
            return $propertyMetadata;
        }

        $subresourceMetadata = $propertyMetadata->getSubresource();

        return $propertyMetadata->withSubresource(new SubresourceMetadata(
            $this->resolve($subresourceMetadata->getResourceClass()),
            $subresourceMetadata->isCollection(),
            $subresourceMetadata->getMaxDepth())
        );
    }

    /**
     * Recursively replaces placeholders with the service container parameters.
     *
     * @see https://github.com/symfony/symfony/blob/6fec32c/src/Symfony/Bundle/FrameworkBundle/Routing/Router.php
     *
     * @param mixed $value The source which might contain "%placeholders%"
     *
     * @throws \RuntimeException When a container value is not a string or a numeric value
     *
     * @return mixed The source with the placeholders replaced by the container
     *               parameters. Arrays are resolved recursively.
     * @psalm-suppress all
     */
    private function resolve($value)
    {
        if (null === $this->container) {
            return $value;
        }

        if (\is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = $this->resolve($val);
            }

            return $value;
        }

        if (!\is_string($value)) {
            return $value;
        }

        $escapedValue = preg_replace_callback('/%%|%([^%\s]++)%/', function ($match) use ($value) {
            $parameter = $match[1];

            // skip %%
            if (!isset($parameter)) {
                return '%%';
            }

            if (preg_match('/^env\(\w+\)$/', $parameter)) {
                throw new \RuntimeException(sprintf('Using "%%%s%%" is not allowed in routing configuration.', $parameter));
            }

            if (\array_key_exists($parameter, $this->collectedParameters)) {
                return $this->collectedParameters[$parameter];
            }

            if ($this->container instanceof SymfonyContainerInterface) {
                $resolved = $this->container->getParameter($parameter);
            } else {
                $resolved = $this->container->get($parameter);
            }

            if (\is_string($resolved) || is_numeric($resolved)) {
                $this->collectedParameters[$parameter] = $resolved;

                return (string) $resolved;
            }

            throw new \RuntimeException(sprintf('The container parameter "%s", used in the resource configuration value "%s", must be a string or numeric, but it is of type %s.', $parameter, $value, \gettype($resolved)));
        }, $value);

        return str_replace('%%', '%', $escapedValue);
    }
}
