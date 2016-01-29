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

/**
 * Repository interface for model which needs number uniqueness check before applying (like random numbers)
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
interface HashSubjectRepositoryInterface
{
    /**
     * Is the given number used?
     *
     * @param $number string
     *
     * @return bool
     */
    public function isNumberUsed($number);
}
