<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Sequence\Manager;

/**
 * Interface SequenceManagerInterface
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
interface SequenceManagerInterface
{
    /**
     * Increment the index and return the new index of the given type
     *
     * @param $type string
     * @return int
     */
    public function setNextIndex($type);
}
