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

use Doctrine\Common\Collections\Collection;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface RootMetadataInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param string $key
     */
    public function setKey($key);

    /**
     * @return string
     */
    public function getKey();

    /**
     * @param MetadataInterface $metadata
     */
    public function setMetadata(MetadataInterface $metadata);

    /**
     * @return MetadataInterface
     */
    public function getMetadata();

    /**
     * @param RootMetadataInterface $rootMetadata
     */
    public function setParent(RootMetadataInterface $rootMetadata);

    /**
     * @return RootMetadataInterface|null
     */
    public function getParent();

    /**
     * @return boolean
     */
    public function hasParent();

    /**
     * @param RootMetadataInterface $rootMetadata
     */
    public function addChild(RootMetadataInterface $rootMetadata);

    /**
     * @param RootMetadataInterface $rootMetadata
     */
    public function removeChild(RootMetadataInterface $rootMetadata);

    /**
     * @return Collection|RootMetadataInterface[]
     */
    public function getChildren();
}
