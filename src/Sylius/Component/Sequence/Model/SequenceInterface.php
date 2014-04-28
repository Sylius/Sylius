<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Sequence\Model;

interface SequenceInterface
{
    /**
     * Return sequence type
     * @return string
     */
    public function getType();

    /**
     * Get the sequence index
     * @return integer
     */
    public function getIndex();

    /**
     * Increment sequence type
     * @return self
     */
    public function incrementIndex();
}
