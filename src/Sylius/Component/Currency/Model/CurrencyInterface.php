<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Currency\Model;

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CurrencyInterface extends
    CodeAwareInterface,
    TimestampableInterface,
    ToggleableInterface,
    ResourceInterface
{
    /**
     * Get the human-friendly name.
     *
     * @return string
     */
    public function getName();

    /**
     * @return float
     */
    public function getExchangeRate();

    /**
     * @param float $rate
     */
    public function setExchangeRate($rate);

    /**
     * @return bool
     */
    public function isBase();

    /**
     * @param bool $base
     */
    public function setBase($base);
}
