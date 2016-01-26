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

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface MetadataInterface
{
    /**
     * @param MetadataInterface $metadata
     */
    public function merge(MetadataInterface $metadata);

    /**
     * @return array
     */
    public function toArray();

    /**
     * @param callable $callable
     */
    public function forAll(callable $callable);
}
