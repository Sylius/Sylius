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

use Sylius\Component\Resource\Model\GetIdInterface;

/**
 * Sylius order Identity model.
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 */
interface IdentityInterface extends GetIdInterface, OrderAwareInterface
{
    /**
     * Get identity name
     *
     * @return string
     */
    public function getName();

    /**
     * Get identity value
     *
     * @return string
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
     * @param string $value
     */
    public function setValue($value);
}
