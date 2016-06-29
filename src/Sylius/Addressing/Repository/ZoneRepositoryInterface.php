<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Addressing\Repository;

use Sylius\Addressing\Model\ZoneInterface;
use Sylius\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ZoneRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $name
     *
     * @return ZoneInterface|null
     */
    public function findOneByName($name);
}
