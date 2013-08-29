<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\Model;

use Sylius\Bundle\ResourceBundle\Model\TimestampableInterface;

interface ExchangeRateInterface extends TimestampableInterface
{
    public function getCurrency();
    public function setCurrency($currency);
    public function getRate();
    public function setRate($rate);
}
