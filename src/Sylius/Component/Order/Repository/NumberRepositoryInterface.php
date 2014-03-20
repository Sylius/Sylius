<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Repository;

use Sylius\Component\Resource\Repository\RepositoryInterface;

interface NumberRepositoryInterface extends RepositoryInterface
{
    public function getLastNumber();

    public function isUsed($number);
}
