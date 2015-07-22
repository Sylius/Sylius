<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Channel\Model;

use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * Channel interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ChannelInterface extends TimestampableInterface
{
    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    public function getDescription();
    public function setDescription($description);
    public function getUrl();
    public function setUrl($url);

    /**
     * @return string
     */
    public function getColor();

    /**
     * @param string $color
     */
    public function setColor($color);

    /**
     * @return Boolean
     */
    public function isEnabled();

    /**
     * @param Boolean $enabled
     */
    public function setEnabled($enabled);
}
