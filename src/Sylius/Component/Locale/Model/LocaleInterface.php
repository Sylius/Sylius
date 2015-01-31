<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Locale\Model;

use Sylius\Component\Resource\Model\GetIdInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * Locale interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface LocaleInterface extends GetIdInterface, TimestampableInterface
{
    /**
     * Get locale code.
     *
     * @return string
     */
    public function getCode();

    /**
     * Set code.
     *
     * @param string $code
     */
    public function setCode($code);

    /**
     * Is activated?
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Set activation status.
     *
     * @param bool $enabled
     */
    public function setEnabled($enabled);
}
