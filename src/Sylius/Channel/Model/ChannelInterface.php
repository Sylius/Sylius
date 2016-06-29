<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Channel\Model;

use Sylius\Resource\Model\CodeAwareInterface;
use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\TimestampableInterface;
use Sylius\Resource\Model\ToggleableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ChannelInterface extends CodeAwareInterface, TimestampableInterface, ToggleableInterface, ResourceInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getHostname();

    /**
     * @param string $hostname
     */
    public function setHostname($hostname);

    /**
     * @return string
     */
    public function getColor();

    /**
     * @param string $color
     */
    public function setColor($color);
}
