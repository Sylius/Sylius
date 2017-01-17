<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Repository;

use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ShipmentRepositoryInterface extends RepositoryInterface
{
    /**
     * @param mixed $id
     * @param mixed $orderId
     *
     * @return ShipmentInterface|null
     */
    public function findOneByOrderId($id, $orderId);

    /**
     * @param string $name
     * @param string $locale
     *
     * @return ShipmentInterface[]
     */
    public function findByName($name, $locale);
}
