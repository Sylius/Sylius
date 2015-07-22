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

use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * Interface for the model representing a currency configured in app.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CurrencyInterface extends TimestampableInterface
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
     * @return Boolean
     */
    public function isEnabled();

    /**
     * @param Boolean $enabled
     */
    public function setEnabled($enabled);
}
