<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Sequence\Repository;

use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Repository interface for Sequence, basically index storing
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
interface SequenceRepositoryInterface extends RepositoryInterface
{
    /**
     * Return the last used index of the given type
     *
     * @param $type string
     * @return int
     */
    public function getLastIndex($type);

    /**
     * Increment index and return the new index of the given type
     *
     * @param $type string
     */
    public function incrementIndex($type);
}
