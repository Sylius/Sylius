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
 * Sylius order Identity model.
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 */
interface IdentityInterface
{
    /**
     * Get identity name
     *
     * @return string $name
     */
    public function getName();

    /**
     * Get identity value
     *
     * @param string $value
     */
    public function getValue();

    /**
     * Set identity name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Set identity value
     *
     * @return string
     */
    public function setValue($value);

    /**
     * Return order.
     *
     * @return OrderInterface
     */
    public function getOrder();

    /**
     * Set order.
     *
     * @param OrderInterface $order
     */
    public function setOrder(OrderInterface $order = null);

}
