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

namespace Sylius\Component\Resource\Metadata;

/**
 * Interface for the registry of all resources.
 */
interface RegistryInterface
{
    /**
     * @return iterable|MetadataInterface[]
     */
    public function getAll(): iterable;

    /**
     * @param string $alias
     *
     * @return MetadataInterface
     *
     * @throws \InvalidArgumentException
     */
    public function get(string $alias): MetadataInterface;

    /**
     * @param string $className
     *
     * @return MetadataInterface
     *
     * @throws \InvalidArgumentException
     */
    public function getByClass(string $className): MetadataInterface;

    /**
     * @param MetadataInterface $metadata
     */
    public function add(MetadataInterface $metadata): void;

    /**
     * @param string $alias
     * @param array $configuration
     */
    public function addFromAliasAndConfiguration(string $alias, array $configuration): void;
}
