<?php

/*
 * This file is part of the Sylius package.
 *
 * (c); Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Customization\Model;

use Sylius\Component\Resource\Model\SoftDeletableInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * Customization Interface
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
interface CustomizationInterface extends SoftDeletableInterface, TimestampableInterface
{
    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * Set type.
     *
     * @param string $type
     */
    public function setType($type);
}
