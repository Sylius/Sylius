<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Metadata\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class RootMetadata implements RootMetadataInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var RootMetadataInterface
     */
    protected $parent;

    /**
     * @var Collection|RootMetadataInterface[]
     */
    protected $children;

    /**
     * @var MetadataInterface
     */
    protected $metadata;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetadata(MetadataInterface $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(RootMetadataInterface $rootMetadata)
    {
        $this->parent = $rootMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParent()
    {
        return null !== $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(RootMetadataInterface $rootMetadata)
    {
        $this->children->add($rootMetadata);
    }

    /**
     * {@inheritdoc}
     */
    public function removeChild(RootMetadataInterface $rootMetadata)
    {
        $this->children->removeElement($rootMetadata);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return $this->children;
    }
}
