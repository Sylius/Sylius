<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Model;

/**
 * @author Daniel Kucharski <daniel@xerias.be>
 */
interface IdentityInterface
{
    /**
     * @return string $name
     */
    public function getName();

    /**
     * @return string $value
     */
    public function getValue();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @param string $value
     *
     * @return OrderInterface
     */
    public function setValue($value);

    /**
     * @return OrderInterface
     */
    public function getOrder();

    /**
     * @param OrderInterface $order
     */
    public function setOrder(OrderInterface $order = null);
}
