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
     * @return string
     */
    public function getKey();

    /**
     * @param string $key
     */
    public function setKey($key);

    /**
     * @return MetadataInterface
     */
    public function getMetadata();

    /**
     * @param MetadataInterface $metadata
     */
    public function setMetadata(MetadataInterface $metadata);
}
