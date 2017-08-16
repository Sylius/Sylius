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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class Registry implements RegistryInterface
{
    /**
     * @var array
     */
    private $metadata = [];

    /**
     * @var array
     */
    private $metadataByClass = [];

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        return $this->metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function get($alias)
    {
        if (!isset($this->metadata[$alias])) {
            throw new \InvalidArgumentException(sprintf('Resource "%s" does not exist.', $alias));
        }

        return $this->metadata[$alias];
    }

    /**
     * {@inheritdoc}
     */
    public function has($alias)
    {
        return isset($this->metadata[$alias]);
    }

    /**
     * {@inheritdoc}
     */
    public function getByClass($className)
    {
        if (!isset($this->metadataByClass[$className])) {
            throw new \InvalidArgumentException(sprintf('Resource with model class "%s" does not exist.', $className));
        }

        return $this->metadataByClass[$className];
    }

    /**
     * {@inheritdoc}
     */
    public function hasByClass($className)
    {
        return isset($this->metadataByClass[$className]);
    }

    /**
     * {@inheritdoc}
     */
    public function add(MetadataInterface $metadata)
    {
        $this->metadata[$metadata->getAlias()] = $metadata;
        $this->metadataByClass[$metadata->getClass('model')] = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function addFromAliasAndConfiguration($alias, array $configuration)
    {
        $this->add(Metadata::fromAliasAndConfiguration($alias, $configuration));
    }
}
