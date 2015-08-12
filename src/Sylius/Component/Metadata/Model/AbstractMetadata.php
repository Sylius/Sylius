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
 * Base metadata class with reusable merging ability.
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
abstract class AbstractMetadata implements MetadataInterface
{
    /**
     * {@inheritdoc}
     */
    public function merge(MetadataInterface $metadata)
    {
        if (!$metadata instanceof self || !$this instanceof $metadata) {
            throw new \InvalidArgumentException(
                sprintf(
                    'You can only merge instances of the same classes. Tried to merge "%s" with "%s".',
                    get_class($this),
                    get_class($metadata)
                )
            );
        }

        $inheritedVariables = get_object_vars($metadata);
        foreach ($inheritedVariables as $inheritedKey => $inheritedValue) {
            if (null !== $this->$inheritedKey) {
                continue;
            }

            if ($this->$inheritedKey instanceof MetadataInterface) {
                $this->$inheritedKey->merge($inheritedValue);
            } else {
                $this->$inheritedKey = $inheritedValue;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return get_object_vars($this);
    }
}
